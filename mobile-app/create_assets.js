const fs = require('fs');
const path = require('path');

// Create a simple SVG that can be used as placeholder
const createSVG = (width, height, color, text) => `
<svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="${color}"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="24" fill="white" text-anchor="middle" dominant-baseline="middle">${text}</text>
</svg>
`;

// Create assets directory if it doesn't exist
const assetsDir = path.join(__dirname, 'assets');
if (!fs.existsSync(assetsDir)) {
  fs.mkdirSync(assetsDir, { recursive: true });
}

// Create icon.png (1024x1024)
const iconSVG = createSVG(1024, 1024, '#FF6B35', 'NH');
fs.writeFileSync(path.join(assetsDir, 'icon.svg'), iconSVG);

// Create adaptive-icon.png (1024x1024)
const adaptiveIconSVG = createSVG(1024, 1024, '#FF6B35', 'NH');
fs.writeFileSync(path.join(assetsDir, 'adaptive-icon.svg'), adaptiveIconSVG);

// Create splash.png (1284x2778 for iPhone 12 Pro Max)
const splashSVG = createSVG(1284, 2778, '#FF6B35', 'Nandini Hub');
fs.writeFileSync(path.join(assetsDir, 'splash.svg'), splashSVG);

console.log('✅ Asset files created successfully!');
console.log('Note: These are SVG placeholders. For production, replace with proper PNG files.');
