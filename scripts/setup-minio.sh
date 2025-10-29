#!/bin/sh

# Set up MinIO alias
echo "Setting up MinIO alias..."
mc alias set myminio http://minio:9000 ${MINIO_ROOT_USER} ${MINIO_ROOT_PASSWORD}

# Wait for MinIO to be ready
echo "Waiting for MinIO to be ready..."
max_attempts=30
attempt=0
until mc admin info myminio >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ $attempt -ge $max_attempts ]; then
        echo "MinIO failed to start after $max_attempts attempts"
        exit 1
    fi
    echo "Attempt $attempt/$max_attempts: MinIO not ready yet, waiting..."
    sleep 2
done
echo "MinIO is ready!"

# Check if user exists and create if not
if ! mc admin user info myminio ${MINIO_USER} >/dev/null 2>&1; then
    echo "Creating user ${MINIO_USER}..."
    mc admin user add myminio ${MINIO_USER} ${MINIO_PASSWORD}
    mc admin policy attach myminio readwrite --user ${MINIO_USER}
    mc admin accesskey create myminio ${MINIO_USER}
    echo "User ${MINIO_USER} created successfully"
else
    echo "User ${MINIO_USER} already exists, skipping creation"
fi

# Create bucket if it doesn't exist
if ! mc ls myminio/${S3_BUCKET_NAME} >/dev/null 2>&1; then
    echo "Creating bucket ${S3_BUCKET_NAME}..."
    mc mb myminio/${S3_BUCKET_NAME}
    echo "Bucket ${S3_BUCKET_NAME} created successfully"
else
    echo "Bucket ${S3_BUCKET_NAME} already exists, skipping creation"
fi

exit 0