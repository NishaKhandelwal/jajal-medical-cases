# Deployment Plan

## Proposed AWS Architecture

The Medical Case Management application could be hosted on AWS using the following services:

- **Amazon EC2:** Host the Laravel application using PHP and a web server such as Nginx. Configure the server to serve the application's `public` directory.
- **Amazon RDS for MySQL:** Store user accounts, roles and medical case records in a managed MySQL database. Enable automated backups and keep the database private.
- **Amazon S3:** Store uploaded documents or other application files, if file uploads are introduced. Keep the bucket private and grant access through an IAM role.
- **Security Groups:** Allow HTTPS traffic on port 443 to the public application entry point and restrict SSH access to authorized administrators. Allow MySQL traffic on port 3306 only from the application's security group.
- **Environment Secrets:** Store the application key, database credentials and other sensitive configuration in AWS Secrets Manager or Systems Manager Parameter Store. Do not commit `.env` files or secrets to Git.
- **HTTPS:** Use an Application Load Balancer with AWS Certificate Manager to provide HTTPS and redirect HTTP traffic to HTTPS.

The deployment process would include configuring the EC2 environment, creating the RDS database, setting production environment variables, running Laravel migrations, building frontend assets, and verifying authentication, role permissions and API endpoints. Logs and monitoring would be configured through Amazon CloudWatch.

This is a proposed deployment architecture. The application has not been deployed to AWS as part of this assignment.