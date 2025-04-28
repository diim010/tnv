#!/bin/bash

# Tonna Vinyla (TNV) WordPress Plugin Setup Script
# Creates MVC folder structure for vinyl record management with WooCommerce integration

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Setting up Tonna Vinyla (TNV) WordPress Plugin...${NC}"

# Define plugin directory (current directory by default)
PLUGIN_DIR="tonna-vinyla"

# Create main plugin directory
mkdir -p $PLUGIN_DIR
echo -e "${GREEN}Created main plugin directory: $PLUGIN_DIR${NC}"

# Create MVC structure
# Core files
mkdir -p $PLUGIN_DIR/includes
touch $PLUGIN_DIR/tonna-vinyla.php
touch $PLUGIN_DIR/includes/class-tonna-vinyla.php
touch $PLUGIN_DIR/includes/class-tnv-loader.php
touch $PLUGIN_DIR/includes/class-tnv-i18n.php
touch $PLUGIN_DIR/includes/class-tnv-activator.php
touch $PLUGIN_DIR/includes/class-tnv-deactivator.php

# Models - Data layer
mkdir -p $PLUGIN_DIR/models
touch $PLUGIN_DIR/models/class-tnv-vinyl-product.php
touch $PLUGIN_DIR/models/class-tnv-discogs-api.php
touch $PLUGIN_DIR/models/class-tnv-music-preview.php

# Views - Presentation layer
mkdir -p $PLUGIN_DIR/views/admin/partials
mkdir -p $PLUGIN_DIR/views/admin/js
mkdir -p $PLUGIN_DIR/views/admin/css
mkdir -p $PLUGIN_DIR/views/public/partials
mkdir -p $PLUGIN_DIR/views/public/js
mkdir -p $PLUGIN_DIR/views/public/css

# Controllers - Business logic
mkdir -p $PLUGIN_DIR/controllers
touch $PLUGIN_DIR/controllers/class-tnv-product-controller.php
touch $PLUGIN_DIR/controllers/class-tnv-admin-controller.php
touch $PLUGIN_DIR/controllers/class-tnv-qr-controller.php
touch $PLUGIN_DIR/controllers/class-tnv-api-controller.php

# Assets
mkdir -p $PLUGIN_DIR/assets/images
mkdir -p $PLUGIN_DIR/assets/js
mkdir -p $PLUGIN_DIR/assets/css
touch $PLUGIN_DIR/assets/js/qr-scanner.js

# Internationalization
mkdir -p $PLUGIN_DIR/languages

echo -e "${GREEN}Created TNV plugin MVC directory structure with necessary files${NC}"
echo -e "${BLUE}Plugin structure setup complete!${NC}"

# Create empty index.php files in each directory for security
find $PLUGIN_DIR -type d -exec touch {}/index.php \;
echo -e "${GREEN}Created index.php files in all directories for security${NC}"

echo -e "${BLUE}=============================================${NC}"
echo -e "${BLUE}Next steps:${NC}"
echo -e "${BLUE}1. Implement the main plugin file${NC}"
echo -e "${BLUE}2. Set up WooCommerce custom fields for vinyl records${NC}"
echo -e "${BLUE}3. Integrate with Discogs API${NC}"
echo -e "${BLUE}4. Add QR scanning functionality${NC}"
echo -e "${BLUE}5. Implement music preview with Spotify/YouTube${NC}"
echo -e "${BLUE}=============================================${NC}"

# Make script executable
chmod +x setup-tonna-vinyla.sh