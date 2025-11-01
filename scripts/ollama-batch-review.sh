#!/usr/bin/env bash

# Batch Code Review Using Ollama
# Reviews all PHP files in specified directories

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
INSTANCE="${1:-rtx3060}"  # Default to RTX3060
OUTPUT_DIR="ollama-reviews"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   Ollama Batch Code Review - Laravel FSM Phase 1          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${GREEN}Instance: ${INSTANCE}${NC}"
echo -e "${GREEN}Timestamp: ${TIMESTAMP}${NC}"
echo ""

# Create output directory
mkdir -p "${OUTPUT_DIR}/${TIMESTAMP}"

# Directories to review
DIRS=(
    "app/Models"
    "app/Services"
    "app/Http/Controllers"
    "app/Filament/Resources"
    "app/Console/Commands"
    "database/seeders"
)

# Check if Python client exists
if [ ! -f "scripts/ollama-client.py" ]; then
    echo -e "${RED}Error: scripts/ollama-client.py not found${NC}"
    echo "Run this script from the repository root directory"
    exit 1
fi

# Function to review a file
review_file() {
    local file=$1
    local relative_path=$(echo "$file" | sed 's|^./||')
    local output_file="${OUTPUT_DIR}/${TIMESTAMP}/${relative_path}.review.md"

    # Create subdirectories if needed
    mkdir -p "$(dirname "$output_file")"

    echo -e "${YELLOW}Reviewing: ${relative_path}${NC}"

    # Run review
    python3 scripts/ollama-client.py \
        --instance "${INSTANCE}" \
        --review "${file}" \
        --language php \
        > "${output_file}" 2>&1

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Review saved to: ${output_file}${NC}"
    else
        echo -e "${RED}✗ Failed to review: ${relative_path}${NC}"
    fi
}

# Count total files
total_files=0
for dir in "${DIRS[@]}"; do
    if [ -d "$dir" ]; then
        count=$(find "$dir" -name "*.php" | wc -l)
        total_files=$((total_files + count))
    fi
done

echo -e "${BLUE}Found ${total_files} PHP files to review${NC}"
echo ""

# Review each directory
current=0
for dir in "${DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        echo -e "${YELLOW}Skipping ${dir} (not found)${NC}"
        continue
    fi

    echo -e "${BLUE}═══ Reviewing ${dir} ═══${NC}"

    find "$dir" -name "*.php" | while read file; do
        current=$((current + 1))
        echo -e "${BLUE}[${current}/${total_files}]${NC}"
        review_file "$file"
        echo ""
    done
done

# Generate summary report
SUMMARY_FILE="${OUTPUT_DIR}/${TIMESTAMP}/SUMMARY.md"

echo "# Code Review Summary" > "${SUMMARY_FILE}"
echo "" >> "${SUMMARY_FILE}"
echo "**Date:** $(date)" >> "${SUMMARY_FILE}"
echo "**Instance:** ${INSTANCE}" >> "${SUMMARY_FILE}"
echo "**Total Files Reviewed:** ${total_files}" >> "${SUMMARY_FILE}"
echo "" >> "${SUMMARY_FILE}"
echo "## Files Reviewed" >> "${SUMMARY_FILE}"
echo "" >> "${SUMMARY_FILE}"

find "${OUTPUT_DIR}/${TIMESTAMP}" -name "*.review.md" -not -name "SUMMARY.md" | sort | while read review; do
    relative=$(echo "$review" | sed "s|${OUTPUT_DIR}/${TIMESTAMP}/||" | sed 's|.review.md$||')
    echo "- [$relative]($review)" >> "${SUMMARY_FILE}"
done

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                  Review Complete!                          ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}Summary: ${SUMMARY_FILE}${NC}"
echo -e "${BLUE}Reviews: ${OUTPUT_DIR}/${TIMESTAMP}/${NC}"
echo ""
echo -e "${YELLOW}To view summary:${NC}"
echo -e "  cat ${SUMMARY_FILE}"
echo ""
echo -e "${YELLOW}To review a specific file:${NC}"
echo -e "  cat ${OUTPUT_DIR}/${TIMESTAMP}/app/Models/User.php.review.md"
echo ""
