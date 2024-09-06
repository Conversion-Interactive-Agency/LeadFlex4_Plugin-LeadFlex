/** @type {import("tailwindcss").Config} */
module.exports = {
    content: [
        "./src/templates/**/*.{twig,html}"
    ],
    darkMode: "class", // or "media" or "class"
    theme: {
        container: {
            center: true,
        },

        screens: {
            "xs": "393px",
            "sm": "640px",
            "md": "768px",
            "lg": "1024px",
            "xl": "1280px",
            "wide": "1700px"
        },
        screens: {
            "wide": "1700px",
            // => @media (min-width: 1700px) { ... }
        },
        plugins: [],
    }
};
