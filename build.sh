#!/bin/bash

# Build script for Claude AI Integration WordPress Plugin
# This script creates a distributable ZIP file for WordPress plugin installation

set -e

PLUGIN_SLUG="claude-ai-integration"
BUILD_DIR="build"
ZIP_NAME="${PLUGIN_SLUG}.zip"

echo "🔨 Building ${PLUGIN_SLUG} plugin..."

# Remove old build files
if [ -f "${ZIP_NAME}" ]; then
    echo "📦 Removing old ${ZIP_NAME}..."
    rm "${ZIP_NAME}"
fi

if [ -d "${BUILD_DIR}" ]; then
    echo "🧹 Cleaning old build directory..."
    rm -rf "${BUILD_DIR}"
fi

# Create build directory
echo "📁 Creating build directory..."
mkdir -p "${BUILD_DIR}"

# Copy plugin files to build directory
echo "📋 Copying plugin files..."
cp -r "${PLUGIN_SLUG}" "${BUILD_DIR}/"

# Remove development files from build
echo "🧹 Removing development files..."
cd "${BUILD_DIR}/${PLUGIN_SLUG}"

# Remove common development files if they exist
rm -f .DS_Store
rm -rf __MACOSX
find . -name ".DS_Store" -delete
find . -name "Thumbs.db" -delete
find . -name ".git*" -delete 2>/dev/null || true

cd ../..

# Create ZIP file
echo "📦 Creating ${ZIP_NAME}..."
cd "${BUILD_DIR}"
zip -r "../${ZIP_NAME}" "${PLUGIN_SLUG}" -x "*.git*" -x "*__MACOSX*" -x "*.DS_Store" >/dev/null
cd ..

# Clean up build directory
echo "🧹 Cleaning up..."
rm -rf "${BUILD_DIR}"

echo "✅ Build complete! Plugin package created: ${ZIP_NAME}"
echo ""
echo "To install in WordPress:"
echo "1. Go to WordPress Admin → Plugins → Add New → Upload Plugin"
echo "2. Upload ${ZIP_NAME}"
echo "3. Click 'Install Now' and then 'Activate'"
