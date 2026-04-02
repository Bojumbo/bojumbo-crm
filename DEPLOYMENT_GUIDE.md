# 🚀 Bojumbo CRM: Автоматизоване розгортання (CI/CD)

Ця інструкція допоможе тобі налаштувати сервер так, щоб він **автоматично оновлювався** при кожному твоєму пуші (`push`) у GitHub.

---

## 1. Підготовка сервера (Proxmox + Docker)
Якщо ти ще не встановив Docker на свою Ubuntu VM у Proxmox:
```bash
# Встановити Docker
curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh
# Запустити Portainer
sudo docker run -d -p 9443:9443 --name portainer --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer-ce:latest
```

---

## 2. Докер-файли у твоєму проекті
Переконайся, що у твоєму репозиторії GitHub є ці файли (вони потрібні Portainer для збірки):

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

---

## 3. Налаштування автоматизації в Portainer
Тепер ми не будемо просто копіювати файли. Ми з'єднаємо Portainer безпосередньо з GitHub:

1. Зайди в Portainer -> **Stacks** -> **Add Stack**.
2. Обери **Build Method**: `Repository`.
3. Заповни дані:
   * **Name:** `bojumbo-crm`
   * **Repository URL:** `https://github.com/Bojumbo/bojumbo-crm`
   * **Repository reference:** `refs/heads/main` (або твоя основна гілка).
   * **Compose path:** `docker-compose.yml`
4. **Увімкни Automatic Updates:**
   * Встанови перемикач **Enabled**.
   * Оберіть **Polling** (наприклад, кожні 5 хвилин).
   * Тепер Portainer сам буде перевіряти GitHub і оновлювати CRM при змінах коду.

---

## 4. Конфігурація середовища (Environment)
У налаштуваннях Stack у Portainer додай змінні середовища (**Environment variables**), які раніше були в `.env`:
* `DB_PASSWORD`: (твій пароль від Postgres у Docker)
* `DB_CONNECTION`: `pgsql`
* `APP_URL`: `https://твій-домен.com` (Cloudflare-адреса)
* `FORCE_HTTPS`: `true`

---

## 5. Доступ через Cloudflare Tunnel
1. Створи тунель у панелі Cloudflare Zero Trust.
2. Встанови конектор на сервері.
3. Налаштуй **Public Hostname**:
   * **Domain:** `crm.твійдомен.com`
   * **Service:** `http://bojumbo-crm_app:80` (ім'я сервісу з compose).

---
**Тепер твій робочий процес виглядає так:**
**Пишеш код локально** -> **Робиш Git Push** -> **Portainer бачить оновлення** -> **CRM сама перезбирається на сервері**. 🚀


Коли все запуститься, база даних буде порожньою. Щоб у тебе з'явилися всі таблиці та поля, не забудь зайти в консоль контейнера bojumbo_crm_app (як я писав у інструкції) і виконати:

```bash
php artisan migrate --force
```
