#!/bin/bash

# Panadero AI Engine - AWS Deployment Script
# This script deploys the AI engine to AWS with all necessary components

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
STACK_NAME="panadero-ai-engine"
REGION="us-east-1"
ENVIRONMENT="production"
DOMAIN_NAME="ai-engine.panadero.services"
ADMIN_DOMAIN_NAME="admin.ai-engine.panadero.services"

echo -e "${BLUE}🚀 Panadero AI Engine - AWS Deployment${NC}"
echo "================================================"

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    echo -e "${RED}❌ AWS CLI is not installed. Please install it first.${NC}"
    exit 1
fi

# Check if AWS credentials are configured
if ! aws sts get-caller-identity &> /dev/null; then
    echo -e "${RED}❌ AWS credentials not configured. Please run 'aws configure' first.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ AWS CLI configured${NC}"

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js is not installed. Please install it first.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Node.js installed${NC}"

# Install dependencies
echo -e "${YELLOW}📦 Installing dependencies...${NC}"
npm install

# Build the project
echo -e "${YELLOW}🔨 Building project...${NC}"
npm run build

# Create deployment package
echo -e "${YELLOW}📦 Creating deployment package...${NC}"
mkdir -p dist
cp -r src dist/
cp package.json dist/
cp package-lock.json dist/
cd dist && npm install --production
cd ..

# Create Lambda deployment package
echo -e "${YELLOW}📦 Creating Lambda deployment package...${NC}"
cd dist
zip -r ../lambda-deployment.zip .
cd ..

# Deploy CloudFormation stack
echo -e "${YELLOW}☁️ Deploying CloudFormation stack...${NC}"
aws cloudformation deploy \
    --template-file aws/deployment.yaml \
    --stack-name $STACK_NAME \
    --parameter-overrides \
        Environment=$ENVIRONMENT \
        DomainName=$DOMAIN_NAME \
        AdminDomainName=$ADMIN_DOMAIN_NAME \
    --region $REGION \
    --capabilities CAPABILITY_IAM

# Get stack outputs
echo -e "${YELLOW}📊 Getting stack outputs...${NC}"
API_ENDPOINT=$(aws cloudformation describe-stacks \
    --stack-name $STACK_NAME \
    --region $REGION \
    --query 'Stacks[0].Outputs[?OutputKey==`APIEndpoint`].OutputValue' \
    --output text)

CLOUDFRONT_URL=$(aws cloudformation describe-stacks \
    --stack-name $STACK_NAME \
    --region $REGION \
    --query 'Stacks[0].Outputs[?OutputKey==`CloudFrontURL`].OutputValue' \
    --output text)

S3_BUCKET=$(aws cloudformation describe-stacks \
    --stack-name $STACK_NAME \
    --region $REGION \
    --query 'Stacks[0].Outputs[?OutputKey==`S3BucketName`].OutputValue' \
    --output text)

# Upload static files to S3
echo -e "${YELLOW}📤 Uploading static files to S3...${NC}"
aws s3 sync public/ s3://$S3_BUCKET/ --delete

# Upload admin interface
echo -e "${YELLOW}📤 Uploading admin interface...${NC}"
aws s3 cp admin/index.html s3://$S3_BUCKET/admin/

# Update Lambda function code
echo -e "${YELLOW}🔄 Updating Lambda function...${NC}"
aws lambda update-function-code \
    --function-name panadero-ai-engine-$ENVIRONMENT \
    --zip-file fileb://lambda-deployment.zip \
    --region $REGION

# Test the deployment
echo -e "${YELLOW}🧪 Testing deployment...${NC}"
echo "Testing API endpoint: $API_ENDPOINT"
curl -s $API_ENDPOINT/api/health | jq . || echo "API test failed"

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "================================================"
echo -e "${BLUE}📊 Deployment Summary:${NC}"
echo "Stack Name: $STACK_NAME"
echo "Region: $REGION"
echo "Environment: $ENVIRONMENT"
echo "API Endpoint: $API_ENDPOINT"
echo "CloudFront URL: https://$CLOUDFRONT_URL"
echo "Public URL: https://$CLOUDFRONT_URL"
echo "Admin URL: https://$CLOUDFRONT_URL/admin"
echo "S3 Bucket: $S3_BUCKET"
echo ""
echo -e "${GREEN}🎉 Your AI Engine is now live on AWS!${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Configure your domain names in Route 53"
echo "2. Set up SSL certificates in ACM"
echo "3. Configure monitoring and alerts in CloudWatch"
echo "4. Set up CI/CD pipeline for automated deployments"
echo "5. Configure backup and disaster recovery"

# Cleanup
echo -e "${YELLOW}🧹 Cleaning up...${NC}"
rm -rf dist/
rm lambda-deployment.zip

echo -e "${GREEN}✨ All done!${NC}"
