# Substream API

A Symfony-based API for processing video streams with automatic subtitle generation and transformation. This application handles the complete workflow from video upload to subtitle generation and formatting.

## Overview

Substream API is a video processing service that:
- Accepts video files or URLs for processing
- Extracts audio from videos
- Generates subtitles using AI/ML services
- Transforms subtitles into different formats (SRT, ASS)
- Manages the entire workflow through a state machine
- Provides webhook endpoints for external service integration

## Features

### Core Functionality
- **Video Processing**: Upload videos via file or URL
- **Audio Extraction**: Extract audio tracks from video files
- **Subtitle Generation**: AI-powered subtitle generation
- **Subtitle Transformation**: Convert subtitles between formats (SRT, ASS)
- **Workflow Management**: State machine-based processing pipeline
- **Webhook Integration**: Real-time status updates via webhooks

### Technical Stack
- **Framework**: Symfony 7.3 with FrankenPHP
- **Database**: PostgreSQL
- **Message Queue**: RabbitMQ with AMQP
- **File Storage**: MinIO (S3-compatible)
- **Authentication**: JWT tokens
- **API**: API Platform for REST endpoints
- **Containerization**: Docker with Docker Compose

## Architecture

The application follows a Domain-Driven Design (DDD) approach with:

- **Entities**: `Stream`, `User`
- **Commands**: Create stream, extract sound, generate subtitles, transform subtitles
- **Message Handlers**: Process asynchronous operations
- **Webhook Consumers**: Handle external service responses
- **Workflow**: State machine managing stream processing states

### Processing Workflow

```
Created → Uploading → Uploaded → Extracting Sound → Generating Subtitle → Transforming Subtitle → Completed
    ↓         ↓          ↓            ↓                    ↓                    ↓
  Failed   Failed    Failed      Failed              Failed              Failed
```

## Getting Started

### Prerequisites
- Docker and Docker Compose
- Make (for using the Makefile commands)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd substream-api
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```

3. **Build and Start**
   ```bash
   make build
   make start
   ```

4. **Initialize Database**
   ```bash
   make db
   make jwt
   make fabric
   ```

5. **Access the Application**
   - API: `https://localhost`
   - RabbitMQ Management: `http://localhost:9003`
   - MinIO Console: `http://localhost:9005`
   - MailDev: `http://localhost:9006`

### TLS Certificates

For local development with HTTPS, trust the local certificate authority:

```bash
# macOS
docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt

# Linux
docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates

# Windows
docker compose cp php:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```

## API Endpoints

### Authentication
- `POST /api/login_check` - Get JWT token
- `GET /api/me` - Get current user info

### Streams
- `GET /api/streams` - List all streams
- `GET /api/streams/{id}` - Get stream details
- `POST /api/streams/video` - Upload video file
- `POST /api/streams/url` - Process video from URL

### Webhooks
- `POST /webhook/getvideosuccess` - Video download success
- `POST /webhook/getvideofailure` - Video download failure
- `POST /webhook/extractsoundsuccess` - Audio extraction success
- `POST /webhook/extractsoundfailure` - Audio extraction failure
- `POST /webhook/generatesubtitlesuccess` - Subtitle generation success
- `POST /webhook/generatesubtitlefailure` - Subtitle generation failure
- `POST /webhook/transformsubtitlesuccess` - Subtitle transformation success
- `POST /webhook/transformsubtitlefailure` - Subtitle transformation failure

## Development

### Available Commands

```bash
# Container Management
make start          # Start all containers
make stop           # Stop all containers
make restart        # Restart containers
make build          # Build containers

# Development
make php            # Enter PHP container shell
make database       # Enter database shell
make install        # Install Composer dependencies
make update         # Update Composer dependencies

# Database
make db             # Reset database with fixtures
make migration      # Create new migration
make migrate        # Run migrations
make fixture        # Load fixtures

# Code Quality
make php-cs-fixer   # Fix code style
make php-stan       # Run static analysis

# Messaging
make fabric         # Setup message transports
make consume        # Consume messages
```

### Project Structure

```
src/
├── Controller/          # HTTP controllers
├── Core/               # Domain logic (DDD)
│   ├── Application/    # Commands, handlers, messages
│   └── Domain/         # Entities, repositories
├── Entity/             # Doctrine entities
├── Enum/               # Enumerations
├── RemoteEvent/        # Webhook consumers
├── Repository/         # Data repositories
├── Service/            # Application services
├── Webhook/            # Webhook request parsers
└── Shared/             # Shared components
```

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Project
PROJECT_NAME=substream-api
SERVER_NAME=localhost

# Database
DATABASE_URL=postgresql://user:password@database:5432/substream

# RabbitMQ
RABBITMQ_USER=guest
RABBITMQ_PASS=guest
RABBITMQ_VHOST=/

# MinIO
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=minioadmin

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase
```

## Deployment

### Production

1. **Build production images**
   ```bash
   docker compose -f compose.yaml -f compose.prod.yaml build
   ```

2. **Start production services**
   ```bash
   docker compose -f compose.yaml -f compose.prod.yaml up -d
   ```

### Monitoring

- **Logs**: Check container logs with `docker compose logs -f [service]`
- **Health**: API health endpoint at `/api/status`
- **Metrics**: Available through Symfony's built-in monitoring

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and code quality checks
5. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions, please open an issue in the repository or contact the development team.