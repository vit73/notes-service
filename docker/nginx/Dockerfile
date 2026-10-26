FROM node:22-alpine AS frontend
WORKDIR /frontend
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY frontend/ ./
RUN npm run build

FROM swaggerapi/swagger-ui:v5.17.14 AS swagger-ui

FROM nginx:1.27-alpine
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend /frontend/dist /usr/share/nginx/html
COPY --from=swagger-ui /usr/share/nginx/html/ /usr/share/nginx/html/docs/
COPY docs/openapi.yaml /usr/share/nginx/html/openapi.yaml
COPY docker/nginx/swagger-initializer.js /usr/share/nginx/html/docs/swagger-initializer.js