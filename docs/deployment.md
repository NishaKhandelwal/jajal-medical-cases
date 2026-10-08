# Deploying to AWS (outline)

- **App server – EC2:** Ubuntu instance running Nginx + PHP-FPM. Deploy with `git pull`, `composer install --no-dev -o`,
  `npm run build`, `php artisan migrate --force`, then `php artisan config:cache route:cache view:cache`.
  Run the queue/scheduler (if used) via systemd/cron.
- **Database – RDS MySQL:** private subnet, not publicly accessible. Security group allows port 3306 **only from the EC2 security group**.
  Enable automated backups.
- **Files – S3:** use `FILESYSTEM_DISK=s3` for uploads. Give the EC2 instance an IAM role with access to just that bucket (no keys in the repo).
- **Security groups:** EC2 allows 80/443 from anywhere and SSH (22) only from my IP; everything else closed.
- **Secrets:** keep `.env` on the server only (or in AWS Secrets Manager / SSM Parameter Store). Set `APP_ENV=production`, `APP_DEBUG=false`, a new `APP_KEY`.
- **HTTPS:** point a domain at the instance (Route 53), issue a free certificate with Let's Encrypt (certbot) or use an ALB with an ACM certificate, and redirect HTTP to HTTPS.
