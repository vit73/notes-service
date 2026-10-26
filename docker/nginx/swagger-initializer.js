window.onload = function () {
    window.ui = SwaggerUIBundle({
        url: '/openapi.yaml',
        urls: undefined,
        configUrl: null,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset,
        ],
        layout: 'StandaloneLayout',
    })
}