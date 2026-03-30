# Deployment — investors.liwacap.com

## Infrastructure

- **Server**: AWS EC2 `i-0a8b2fc3568d5d6d9` (`40.172.174.140`)
- **Region**: me-central-1 (UAE)
- **OS**: Ubuntu 24.04 LTS
- **Web server**: Apache 2.4 with Let's Encrypt SSL
- **PHP**: 8.4 (via ondrej/php PPA)
- **Framework**: Laravel 12
- **Node**: 18.19.1 / npm 9.2.0
- **Database**: RDS MySQL 8.0 (`database-1.c9acmucmctpk.me-central-1.rds.amazonaws.com`, database: `liwa_portal2`)
- **File storage**: S3 bucket `investor-liwa-app` (credentials in `.env`)
- **AWS Account**: 252056599010 (`liwa` CLI profile)

## Server Access

No SSH key is configured. Access is via **AWS SSM (Systems Manager)**:

```bash
# Run a command on the server
aws --profile liwa ssm send-command \
  --instance-ids i-0a8b2fc3568d5d6d9 \
  --document-name "AWS-RunShellScript" \
  --parameters '{"commands":["your-command-here"]}' \
  --query "Command.CommandId" --output text

# Get the output (wait a few seconds first)
aws --profile liwa ssm get-command-invocation \
  --command-id <COMMAND_ID> \
  --instance-id i-0a8b2fc3568d5d6d9 \
  --query "[Status,StandardOutputContent,StandardErrorContent]" \
  --output json
```

For multi-line or complex scripts, base64 encode them to avoid escaping issues:

```bash
SCRIPT='#!/bin/bash
cd /var/www/html
php artisan --version
' && ENCODED=$(echo "$SCRIPT" | base64)

aws --profile liwa ssm send-command \
  --instance-ids i-0a8b2fc3568d5d6d9 \
  --document-name "AWS-RunShellScript" \
  --parameters "{\"commands\":[\"echo $ENCODED | base64 -d | bash\"]}" \
  --query "Command.CommandId" --output text
```

**Important**: SSM runs as root with no `$HOME` set. Prefix commands with:
```bash
export HOME=/root
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/root/.composer
```

For git, also add:
```bash
git config --global --add safe.directory /var/www/html
```

## Code Deployment

The server has a git repo at `/var/www/html` with two remotes:

- `neworigin` → `https://github.com/chrisrowe/liwa-investor.git` (use this one)
- `origin` → `https://github.com/imagino/Liwa-investor.git` (legacy, do not use)

### Steps

1. **Merge and push** your changes to `main` locally
2. **Back up the database** on the server:
   ```bash
   cd /var/www/html
   DB_PASS=$(grep ^DB_PASSWORD .env | head -1 | cut -d= -f2 | tr -d '"')
   mysqldump -h database-1.c9acmucmctpk.me-central-1.rds.amazonaws.com \
     -u admin -p"$DB_PASS" --set-gtid-purged=OFF --single-transaction \
     liwa_portal2 > /tmp/liwa_investor_backup_$(date +%Y%m%d).sql
   ```
3. **Pull the code**:
   ```bash
   cd /var/www/html
   git stash
   git pull neworigin main
   ```
4. **Install PHP dependencies** (if composer.json/lock changed):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
5. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```
6. **Rebuild frontend** (if JS/CSS/Vue changed):
   ```bash
   npm ci --legacy-peer-deps
   npx mix --production
   ```
   The `--legacy-peer-deps` flag is needed due to an Inertia.js peer dependency conflict that is safe to ignore.
7. **Clear and rebuild caches**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
8. **Fix permissions**:
   ```bash
   chown -R www-data:www-data /var/www/html
   chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
   ```

### If caches cause issues

If the site breaks after caching, clear everything:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## Transferring Files via S3

The EC2 instance role does not have S3 write access, but the application's own S3 credentials (in `.env`) do. To move files to/from the server, use Python:

```bash
# Upload from server to S3
cd /var/www/html
export AWS_ACCESS_KEY_ID=$(grep ^AWS_ACCESS_KEY_ID .env | head -1 | cut -d= -f2)
export AWS_SECRET_ACCESS_KEY=$(grep ^AWS_SECRET_ACCESS_KEY .env | head -1 | cut -d= -f2 | tr -d '"')
export AWS_DEFAULT_REGION=$(grep ^AWS_DEFAULT_REGION .env | head -1 | cut -d= -f2)

python3 << 'EOF'
import boto3, os
s3 = boto3.client("s3",
    aws_access_key_id=os.environ["AWS_ACCESS_KEY_ID"],
    aws_secret_access_key=os.environ["AWS_SECRET_ACCESS_KEY"],
    region_name=os.environ["AWS_DEFAULT_REGION"])
s3.upload_file("/tmp/myfile.sql.gz", "investor-liwa-app", "tmp/myfile.sql.gz")
EOF
```

Then download locally:
```bash
aws --profile liwa s3 cp s3://investor-liwa-app/tmp/myfile.sql.gz ./myfile.sql.gz
```

## Environment

The production `.env` should have:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://investors.liwacap.com
FILESYSTEM_DRIVER=s3
```

The database password contains a `$` character — use the `grep` + `cut` pattern shown above rather than hardcoding it.

## Rollback

1. Restore the database: import your backup SQL file via `mysql`
2. Revert the code: `git checkout <previous-commit>`
3. Re-run `composer install` if dependencies changed
4. Clear caches: `php artisan config:clear && php artisan route:clear`

## Last Deployed

- **Date**: 30 March 2026
- **Commit**: `c0361a0` (Laravel 12 upgrade)
- **PHP upgraded**: 8.0 → 8.4
