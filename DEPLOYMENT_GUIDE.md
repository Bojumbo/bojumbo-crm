# 🚀 Bojumbo CRM: Повна інструкція по розгортанню на сервері

Цей документ допоможе тобі перенести проект з локального комп'ютера на власний сервер з Proxmox.

## 1. Підготовка Віртуальної Машини (Proxmox)
1. **Створи VM:** Ubuntu Server 24.04 LTS.
2. **Характеристики:** 2 Cores, 4GB RAM, 40GB Disk.
3. **Встанови Docker:**
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh
   ```
4. **Встанови Portainer:**
   ```bash
   sudo docker run -d -p 9443:9443 --name portainer --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer-ce:latest
   ```

## 2. Docker-конфігурація проекту

### Dockerfile (створи в корені проекту)
```dockerfile
FROM php:8.4-fpm
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev nginx nodejs npm
RUN docker-php-ext-install pdo_pgsql mbstring gd bcmath
WORKDIR /var/www/html
COPY . .
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN chown -R www-data:www-data storage bootstrap/cache
```

### docker-compose.yml (використовуй у Portainer Stacks)
```yaml
version: '3.8'
services:
  app:
    build: .
    restart: always
    environment:
      - APP_ENV=production
    ports:
      - "8080:80"
    volumes:
      - .env:/var/www/html/.env
      - storage:/var/www/html/storage
  db:
      image: postgres:18
      restart: always
      environment:
        POSTGRES_DB: bojumbocrm
        POSTGRES_PASSWORD: password123
  redis:
      image: redis:alpine
      restart: always
  worker:
      build: .
      command: php artisan queue:work
      restart: always
      depends_on: [redis, db]
```

## 3. Cloudflare Tunnel
1. Створи тунель у панелі Cloudflare Zero Trust.
2. Встанови конектор на сервері через Docker.
3. Налаштуй **Public Hostname**:
   * **Domain:** твій-домен.com
   * **Service:** `http://app:80` (внутрішня адреса в мережі Docker).

## 4. Google OAuth (Критично)
1. У Google Console онови **Redirect URI**: `https://твій-домен.com/google/callback`.
2. У файлі `.env` на сервері обов'язково вкажи:
   ```env
   APP_URL=https://твій-домен.com
   SESSION_SECURE_COOKIE=true
   ```
   Це дозволить Google працювати через захищене з'єднання Cloudflare.

---
**Також не забудь налаштувати Supervisor або Docker-воркери для черг, щоб автоматизації працювали у фоні.**
