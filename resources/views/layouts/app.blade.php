<!DOCTYPE html>
<html class="light" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <!-- Google Fonts: Public Sans -->
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@100..900&amp;display=swap" rel="stylesheet"/>
        <!-- Material Symbols -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <!-- Tailwind Config -->
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#135bec",
                            "background-light": "#f6f6f8",
                            "background-dark": "#101622",
                            "surface-light": "#ffffff",
                            "surface-dark": "#1e293b",
                            "text-main-light": "#0d121b",
                            "text-main-dark": "#f1f5f9",
                            "text-muted-light": "#4c669a",
                            "text-muted-dark": "#94a3b8",
                        },
                        fontFamily: {
                            "display": ["Public Sans", "sans-serif"]
                        },
                        borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                    },
                },
            }
        </script>
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="font-display bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark antialiased overflow-auto">
        @inertia
    </body>
</html>