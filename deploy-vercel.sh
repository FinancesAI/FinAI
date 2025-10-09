#!/bin/bash

# FinAI Vercel Deployment Script
echo "🚀 Starting FinAI deployment to Vercel..."

# Check if Vercel CLI is installed
if ! command -v vercel &> /dev/null; then
    echo "❌ Vercel CLI is not installed. Please install it first:"
    echo "npm i -g vercel"
    exit 1
fi

# Check if user is logged in
if ! vercel whoami &> /dev/null; then
    echo "🔐 Please login to Vercel first:"
    vercel login
fi

# Pull environment variables
echo "📥 Pulling environment variables..."
vercel env pull .env.local

# Deploy to Vercel
echo "🚀 Deploying to Vercel..."
vercel --prod

# Get deployment URL
echo "🌐 Getting deployment URL..."
DEPLOYMENT_URL=$(vercel ls --json | jq -r '.[0].url')

echo "✅ Deployment completed!"
echo "🌐 Your app is available at: https://$DEPLOYMENT_URL"
echo ""
echo "📋 Next steps:"
echo "1. Add MySQL database in Vercel dashboard"
echo "2. Set up environment variables"
echo "3. Run database initialization: php vercel-init-db.php"
echo ""
echo "📖 For detailed instructions, see VERCEL_DEPLOY.md"
