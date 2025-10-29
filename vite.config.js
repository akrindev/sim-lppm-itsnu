import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
	plugins: [
		laravel({
			input: ["resources/css/app.css", "resources/js/app.js", "resources/js/theme-config.js", "resources/js/dashboard-charts.js"],
			refresh: true,
		}),
	],
	server: {
		cors: true,
	},
});
