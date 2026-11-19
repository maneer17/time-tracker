# ---- Stage 1: build the Vue app ----
FROM node:20-alpine AS build
ARG BUILD_MODE=production
WORKDIR /app
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ .
RUN npm run build -- --mode ${BUILD_MODE}

# ---- Stage 2: serve it with nginx ----
FROM nginx:alpine
ARG NGINX_CONF=default.conf
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/nginx/${NGINX_CONF} /etc/nginx/conf.d/default.conf