const fs = require('fs');
const path = require('path');

// Create a simple base64 encoded 1x1 PNG (transparent)
const createBase64PNG = (width, height, r = 255, g = 107, b = 53) => {
  // This is a minimal PNG header for a solid color image
  // For simplicity, we'll create a very basic PNG structure
  const pngHeader = Buffer.from([
    0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, // PNG signature
    0x00, 0x00, 0x00, 0x0D, // IHDR chunk length
    0x49, 0x48, 0x44, 0x52, // IHDR
    0x00, 0x00, 0x00, 0x01, // Width (1 pixel)
    0x00, 0x00, 0x00, 0x01, // Height (1 pixel)
    0x08, 0x02, 0x00, 0x00, 0x00, // Bit depth, color type, compression, filter, interlace
    0x90, 0x77, 0x53, 0xDE, // CRC
    0x00, 0x00, 0x00, 0x0C, // IDAT chunk length
    0x49, 0x44, 0x41, 0x54, // IDAT
    0x08, 0x99, 0x01, 0x01, 0x00, 0x00, 0x00, 0xFF, 0xFF, 0x00, 0x00, 0x00, 0x02, 0x00, 0x01, // Compressed data
    0x00, 0x00, 0x00, 0x00, // IEND chunk length
    0x49, 0x45, 0x4E, 0x44, // IEND
    0xAE, 0x42, 0x60, 0x82  // CRC
  ]);
  
  return pngHeader;
};

// Create assets directory if it doesn't exist
const assetsDir = path.join(__dirname, 'assets');
if (!fs.existsSync(assetsDir)) {
  fs.mkdirSync(assetsDir, { recursive: true });
}

// Create basic PNG files
const pngData = createBase64PNG();

// Create icon.png
fs.writeFileSync(path.join(assetsDir, 'icon.png'), pngData);

// Create adaptive-icon.png
fs.writeFileSync(path.join(assetsDir, 'adaptive-icon.png'), pngData);

// Create splash.png
fs.writeFileSync(path.join(assetsDir, 'splash.png'), pngData);

// Create favicon.png
fs.writeFileSync(path.join(assetsDir, 'favicon.png'), pngData);

// Create notification-icon.png
fs.writeFileSync(path.join(assetsDir, 'notification-icon.png'), pngData);

// Create a dummy notification sound file
const wavHeader = Buffer.from([
  0x52, 0x49, 0x46, 0x46, // "RIFF"
  0x24, 0x00, 0x00, 0x00, // File size - 8
  0x57, 0x41, 0x56, 0x45, // "WAVE"
  0x66, 0x6D, 0x74, 0x20, // "fmt "
  0x10, 0x00, 0x00, 0x00, // Subchunk1Size
  0x01, 0x00, 0x01, 0x00, // AudioFormat, NumChannels
  0x44, 0xAC, 0x00, 0x00, // SampleRate
  0x88, 0x58, 0x01, 0x00, // ByteRate
  0x02, 0x00, 0x10, 0x00, // BlockAlign, BitsPerSample
  0x64, 0x61, 0x74, 0x61, // "data"
  0x00, 0x00, 0x00, 0x00  // Subchunk2Size
]);

fs.writeFileSync(path.join(assetsDir, 'notification-sound.wav'), wavHeader);

console.log('✅ PNG asset files created successfully!');
console.log('📁 Created files:');
console.log('  - icon.png');
console.log('  - adaptive-icon.png');
console.log('  - splash.png');
console.log('  - favicon.png');
console.log('  - notification-icon.png');
console.log('  - notification-sound.wav');
console.log('');
console.log('⚠️  Note: These are minimal placeholder files.');
console.log('   For production, replace with proper high-quality assets.');
