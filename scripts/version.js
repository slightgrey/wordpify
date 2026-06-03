import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT      = path.resolve(__dirname, '..');

const bump = process.argv[2]; // patch | minor | major

if (!['patch', 'minor', 'major'].includes(bump)) {
  console.error('Usage: npm run version:patch | version:minor | version:major');
  process.exit(1);
}

// --- Read current version from wordpify.php ---
const phpPath    = path.join(ROOT, 'wordpify.php');
const phpContent = fs.readFileSync(phpPath, 'utf8');

const headerMatch = phpContent.match(/\*\s+Version:\s+([\d.]+)/);
if (!headerMatch) {
  console.error('Could not find Version header in wordpify.php');
  process.exit(1);
}

const current = headerMatch[1].trim();
const [major, minor, patch] = current.split('.').map(Number);

// --- Calculate new version ---
let next;
if (bump === 'major') next = `${major + 1}.0.0`;
if (bump === 'minor') next = `${major}.${minor + 1}.0`;
if (bump === 'patch') next = `${major}.${minor}.${patch + 1}`;

console.log(`\n🔖 Bumping version: ${current} → ${next} (${bump})\n`);

// --- Update wordpify.php: plugin header + constant ---
let updatedPhp = phpContent
  .replace(/(\*\s+Version:\s+)[\d.]+/, `$1${next}`)
  .replace(/(define\(\s*'WORDPIFY_VERSION',\s*')[^']+(')/,  `$1${next}$2`);

fs.writeFileSync(phpPath, updatedPhp, 'utf8');
console.log(`  ✔ wordpify.php`);

// --- Update package.json ---
const pkgPath = path.join(ROOT, 'package.json');
const pkg     = JSON.parse(fs.readFileSync(pkgPath, 'utf8'));
pkg.version   = next;
fs.writeFileSync(pkgPath, JSON.stringify(pkg, null, 2) + '\n', 'utf8');
console.log(`  ✔ package.json`);

console.log(`\n✅ Version is now ${next}\n`);
console.log(`   Run "npm run bundle" to build and package the release.\n`);
