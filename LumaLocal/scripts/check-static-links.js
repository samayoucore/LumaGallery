const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..', 'static');
const htmlFiles = fs.readdirSync(root).filter((file) => file.endsWith('.html'));
const missing = [];

for (const file of htmlFiles) {
  const html = fs.readFileSync(path.join(root, file), 'utf8');
  const matches = html.matchAll(/(?:href|src)="([^"]+)"/g);

  for (const match of matches) {
    let url = match[1];
    if (!url || url.startsWith('#') || /^(https?:|mailto:|tel:|data:)/.test(url)) continue;

    url = url.split('#')[0].split('?')[0];
    if (!url) continue;

    const target = path.resolve(root, url);
    if (!target.startsWith(root) || !fs.existsSync(target)) {
      missing.push(`${file} -> ${match[1]}`);
    }
  }
}

if (missing.length) {
  console.error(`Missing local links:\n${missing.join('\n')}`);
  process.exit(1);
}

console.log(`All local href/src targets exist across ${htmlFiles.length} HTML files.`);
