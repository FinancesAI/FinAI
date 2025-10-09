# FinAI Vercel Deployment Script for Windows
Write-Host "🚀 Starting FinAI deployment to Vercel..." -ForegroundColor Green

# Check if Vercel CLI is installed
try {
    vercel --version | Out-Null
    Write-Host "✅ Vercel CLI is installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Vercel CLI is not installed. Please install it first:" -ForegroundColor Red
    Write-Host "npm i -g vercel" -ForegroundColor Yellow
    exit 1
}

# Check if user is logged in
try {
    vercel whoami | Out-Null
    Write-Host "✅ Logged in to Vercel" -ForegroundColor Green
} catch {
    Write-Host "🔐 Please login to Vercel first:" -ForegroundColor Yellow
    vercel login
}

# Pull environment variables
Write-Host "📥 Pulling environment variables..." -ForegroundColor Blue
vercel env pull .env.local

# Deploy to Vercel
Write-Host "🚀 Deploying to Vercel..." -ForegroundColor Blue
vercel --prod

Write-Host "✅ Deployment completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor Yellow
Write-Host "1. Add MySQL database in Vercel dashboard" -ForegroundColor White
Write-Host "2. Set up environment variables" -ForegroundColor White
Write-Host "3. Run database initialization: php vercel-init-db.php" -ForegroundColor White
Write-Host ""
Write-Host "📖 For detailed instructions, see VERCEL_DEPLOY.md" -ForegroundColor Cyan
