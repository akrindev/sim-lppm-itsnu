import '@tabler/core/js/tabler';
import './theme-config';

/**
 * Configuration items for menu and layout settings
 */
const SETTINGS_CONFIG = {
	'menu-position': {
		localStorage: 'tablerMenuPosition',
		default: 'top',
	},
	'menu-behavior': {
		localStorage: 'tablerMenuBehavior',
		default: 'sticky',
	},
	'container-layout': {
		localStorage: 'tablerContainerLayout',
		default: 'boxed',
	},
};

/**
 * Settings Manager Class
 */
class SettingsManager {
	constructor(config) {
		this.config = config;
		this.settings = this.loadSettings();
	}

	/**
	 * Load settings from localStorage or use defaults
	 */
	loadSettings() {
		const settings = {};

		for (const [key, params] of Object.entries(this.config)) {
			const storedValue = localStorage.getItem(params.localStorage);
			settings[key] = storedValue ?? params.default;
		}

		return settings;
	}

	/**
	 * Parse URL parameters and update settings
	 */
	parseUrlParams() {
		const urlParams = new URLSearchParams(window.location.search);

		urlParams.forEach((value, key) => {
			if (this.config[key]) {
				this.updateSetting(key, value);
			}
		});
	}

	/**
	 * Update a single setting
	 */
	updateSetting(key, value) {
		if (this.config[key]) {
			localStorage.setItem(this.config[key].localStorage, value);
			this.settings[key] = value;
		}
	}

	/**
	 * Update form controls to reflect current settings
	 */
	syncFormControls(form) {
		if (!form) return;

		for (const [key, value] of Object.entries(this.settings)) {
			const input = form.querySelector(
				`[name="settings-${key}"][value="${value}"]`,
			);

			if (input) {
				input.checked = true;
			}
		}
	}

	/**
	 * Save settings from form
	 */
	saveFromForm(form) {
		if (!form) return;

		for (const key of Object.keys(this.config)) {
			const checkedInput = form.querySelector(
				`[name="settings-${key}"]:checked`,
			);

			if (checkedInput) {
				this.updateSetting(key, checkedInput.value);
			}
		}

		// Trigger resize event for layout recalculation
		window.dispatchEvent(new Event('resize'));
	}
}

/**
 * Initialize settings functionality
 */
const initializeSettings = () => {
	const settingsManager = new SettingsManager(SETTINGS_CONFIG);
	const settingsForm = document.querySelector('#offcanvasSettings');

	// Parse URL parameters on load
	settingsManager.parseUrlParams();

	// Setup form if it exists
	if (settingsForm) {
		// Sync form controls with current settings
		settingsManager.syncFormControls(settingsForm);

		// Handle form submission
		settingsForm.addEventListener('submit', (event) => {
			event.preventDefault();

			settingsManager.saveFromForm(settingsForm);

			// Hide offcanvas if bootstrap is available
			if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
				const offcanvas = bootstrap.Offcanvas.getInstance(settingsForm) ||
					new bootstrap.Offcanvas(settingsForm);
				offcanvas.hide();
			}
		});
	}
};

document.addEventListener('livewire:navigated', initializeSettings);
