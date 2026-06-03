import archiver from 'archiver';
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT      = path.resolve(__dirname, '..');

// Read version from wordpify.php (source of truth).
const mainFile  = fs.readFileSync(path.join(ROOT, 'wordpify.php'), 'utf8');
const versionMatch = mainFile.match(/\*\s+Version:\s+(.+)/);
const version   = versionMatch ? versionMatch[1].trim() : '1.0.0';

const zipName   = `wordpify-v${version}.zip`;
const zipPath   = path.join(ROOT, zipName);
const pluginDir = 'wordpify'; // folder name inside the zip (must match plugin slug)

console.log(`\n📦 Bundling Wordpify v${version}...\n`);

// 1. Build assets.
console.log('  → Building assets...');
execSync('vite build', { cwd: ROOT, stdio: 'inherit' });

// 2. Verify dist was produced.
if (!fs.existsSync(path.join(ROOT, 'dist/admin/wordpify-admin.js'))) {
  console.error('  ✖ Build failed — dist/admin/wordpify-admin.js not found.');
  process.exit(1);
}

// 3. Remove old zip if exists.
if (fs.existsSync(zipPath)) {
  fs.unlinkSync(zipPath);
}

// 4. Create zip.
console.log(`  → Creating ${zipName}...`);

const output  = fs.createWriteStream(zipPath);
const archive = archiver('zip', { zlib: { level: 9 } });

archive.on('error', (err) => { throw err; });

output.on('close', () => {
  const kb = (archive.pointer() / 1024).toFixed(1);
  console.log(`\n✅ Done! ${zipName} (${kb} KB)\n`);
  console.log(`   Install via WordPress Admin → Plugins → Add New → Upload Plugin\n`);
});

archive.pipe(output);

// Files and folders to include.
const includes = [
  { type: 'dir',  src: 'src',        dest: `${pluginDir}/src` },
  { type: 'dir',  src: 'vendor',     dest: `${pluginDir}/vendor` },
  { type: 'dir',  src: 'dist',       dest: `${pluginDir}/dist` },
  { type: 'dir',  src: 'assets',     dest: `${pluginDir}/assets` },
  { type: 'dir',  src: 'templates',  dest: `${pluginDir}/templates` },
  { type: 'dir',  src: 'languages',  dest: `${pluginDir}/languages` },
  { type: 'file', src: 'wordpify.php',    dest: `${pluginDir}/wordpify.php` },
  { type: 'file', src: 'uninstall.php',   dest: `${pluginDir}/uninstall.php` },
  { type: 'file', src: 'composer.json',   dest: `${pluginDir}/composer.json` },
];

// Add composer.lock if present.
if (fs.existsSync(path.join(ROOT, 'composer.lock'))) {
  includes.push({ type: 'file', src: 'composer.lock', dest: `${pluginDir}/composer.lock` });
}

for (const item of includes) {
  const fullSrc = path.join(ROOT, item.src);
  if (!fs.existsSync(fullSrc)) {
    console.warn(`  ⚠ Skipping missing: ${item.src}`);
    continue;
  }
  if (item.type === 'dir') {
    archive.directory(fullSrc, item.dest);
  } else {
    archive.file(fullSrc, { name: item.dest });
  }
}

archive.finalize();
