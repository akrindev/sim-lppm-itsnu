
import '@tabler/core/dist/libs/nouislider/dist/nouislider.min.js';
import TomSelect from '@tabler/core/dist/libs/tom-select/dist/js/tom-select.base.js';
import '@tabler/core/js/tabler';
import './theme-config';

// Make TomSelect available globally
window.TomSelect = TomSelect;
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

/**
 * TomSelect Configuration
 */
const TOM_SELECT_CONFIG = {
	create: false,
	placeholder: 'Pilih opsi...',
	searchField: ['text'],
	valueField: 'value',
	labelField: 'text',
	copyClassesToDropdown: false,
	dropdownParent: 'body',
	controlInput: '<input>',
	render: {
		item: (data, escapeFunc) => {
			if (data.customProperties) {
				return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escapeFunc(data.text) + '</div>';
			}
			return '<div>' + escapeFunc(data.text) + '</div>';
		},
		option: (data, escapeFunc) => {
			if (data.customProperties) {
				return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escapeFunc(data.text) + '</div>';
			}
			return '<div>' + escapeFunc(data.text) + '</div>';
		},
	},
};

/**
 * Initialize Tom Select for all select elements with class 'tom-select'
 */
const initializeTomSelect = () => {
	const { TomSelect } = window;
	if (!TomSelect) return;

	document.querySelectorAll('select.tom-select:not(.ts-hidden-accessible)').forEach((selectEl) => {
		if (selectEl.tomSelect) {
			selectEl.tomSelect.destroy();
		}

		// Get current value(s) from the select element
		const currentValue = selectEl.value;
		const currentValues = Array.from(selectEl.selectedOptions).map(option => option.value);

		const config = {
			...TOM_SELECT_CONFIG,
			placeholder: selectEl.getAttribute('placeholder') || TOM_SELECT_CONFIG.placeholder,
		};

		// Set initial items if there's a current value
		if (currentValue && currentValue !== '') {
			config.items = [currentValue];
		} else if (currentValues.length > 0) {
			config.items = currentValues;
		}

		new TomSelect(selectEl, config);
	});
};

/**
 * Initialize Tom Select for select elements within a specific element (like a modal)
 */
const initializeTomSelectInElement = (element) => {
	const { TomSelect } = window;
	if (!TomSelect) return;

	element.querySelectorAll('select.tom-select:not(.ts-hidden-accessible)').forEach((selectEl) => {
		if (selectEl.tomSelect) {
			selectEl.tomSelect.destroy();
		}

		// Get current value(s) from the select element
		const currentValue = selectEl.value;
		const currentValues = Array.from(selectEl.selectedOptions).map(option => option.value);

		const config = {
			...TOM_SELECT_CONFIG,
			placeholder: selectEl.getAttribute('placeholder') || TOM_SELECT_CONFIG.placeholder,
		};

		// Set initial items if there's a current value
		if (currentValue && currentValue !== '') {
			config.items = [currentValue];
		} else if (currentValues.length > 0) {
			config.items = currentValues;
		}

		new TomSelect(selectEl, config);
	});
};

/**
 * Livewire Lifecycle Hooks Integration
 * Initialize TomSelect on all relevant Livewire events
 */
document.addEventListener('livewire:init', () => {
	// Register Livewire hooks after Livewire initializes

	// Component initialization hook - fires when a new component is discovered
	Livewire.hook('component.init', ({ component, cleanup }) => {
		// Use queueMicrotask to ensure Livewire has finished setting initial values
		queueMicrotask(() => {
			const selectElements = component.el.querySelectorAll('select.tom-select:not(.ts-hidden-accessible)');
			if (selectElements.length > 0) {
				selectElements.forEach((selectEl) => {
					if (selectEl.tomSelect) {
						selectEl.tomSelect.destroy();
					}

					// Get current value(s) from the select element
					const currentValue = selectEl.value;
					const currentValues = Array.from(selectEl.selectedOptions).map(option => option.value);

					const config = {
						...TOM_SELECT_CONFIG,
						placeholder: selectEl.getAttribute('placeholder') || TOM_SELECT_CONFIG.placeholder,
					};

					// Set initial items if there's a current value
					if (currentValue && currentValue !== '') {
						config.items = [currentValue];
					} else if (currentValues.length > 0) {
						config.items = currentValues;
					}

					new TomSelect(selectEl, config);
				});
			}

			// Cleanup TomSelect instances when component is removed
			cleanup(() => {
				selectElements.forEach((selectEl) => {
					if (selectEl.tomSelect) {
						selectEl.tomSelect.destroy();
					}
				});
			});
		});
	});

	// DOM Morph hooks - fires during morphing phase after network roundtrip
	Livewire.hook('morph.added', ({ el }) => {
		if (el.classList?.contains('tom-select')) {
			// Re-initialize if the select element itself was added
			if (el.tagName === 'SELECT') {
				if (el.tomSelect) {
					el.tomSelect.destroy();
				}

				// Get current value(s) from the select element
				const currentValue = el.value;
				const currentValues = Array.from(el.selectedOptions).map(option => option.value);

				const config = {
					...TOM_SELECT_CONFIG,
					placeholder: el.getAttribute('placeholder') || TOM_SELECT_CONFIG.placeholder,
				};

				// Set initial items if there's a current value
				if (currentValue && currentValue !== '') {
					config.items = [currentValue];
				} else if (currentValues.length > 0) {
					config.items = currentValues;
				}

				new TomSelect(el, config);
			}
		} else if (el.querySelectorAll) {
			// Check for select elements within the added element
			const selectElements = el.querySelectorAll('select.tom-select:not(.ts-hidden-accessible)');
			selectElements.forEach((selectEl) => {
				if (selectEl.tomSelect) {
					selectEl.tomSelect.destroy();
				}

				// Get current value(s) from the select element
				const currentValue = selectEl.value;
				const currentValues = Array.from(selectEl.selectedOptions).map(option => option.value);

				const config = {
					...TOM_SELECT_CONFIG,
					placeholder: selectEl.getAttribute('placeholder') || TOM_SELECT_CONFIG.placeholder,
				};

				// Set initial items if there's a current value
				if (currentValue && currentValue !== '') {
					config.items = [currentValue];
				} else if (currentValues.length > 0) {
					config.items = currentValues;
				}

				new TomSelect(selectEl, config);
			});
		}
	});

	// Before morphing - optionally destroy instances to prevent conflicts
	Livewire.hook('morph.updating', ({ el }) => {
		if (el.classList?.contains('tom-select') && el.tagName === 'SELECT') {
			if (el.tomSelect) {
				el.tomSelect.destroy();
			}
		} else if (el.querySelectorAll) {
			const selectElements = el.querySelectorAll('select.tom-select:not(.ts-hidden-accessible)');
			selectElements.forEach((selectEl) => {
				if (selectEl.tomSelect) {
					selectEl.tomSelect.destroy();
				}
			});
		}
	});

	// After morphing completes for the component
	Livewire.hook('morphed', ({ component }) => {
		// Use setTimeout to ensure DOM is fully updated
		setTimeout(() => {
			initializeTomSelectInElement(component.el);
		}, 0);
	});

	// Commit hook - after server response is processed
	Livewire.hook('commit.success', ({ component }) => {
		// Use setTimeout to ensure DOM is fully updated
		setTimeout(() => {
			initializeTomSelectInElement(component.el);
		}, 0);
	});
});

// Fallback: Initialize on page navigation (e.g., wire:navigate)
document.addEventListener('livewire:navigated', () => {
	initializeSettings();
	initializeTomSelect();
});

// Initialize Tom Select specifically for modal content when modals are shown
document.addEventListener('shown.bs.modal', (event) => {
	const modal = event.target;
	initializeTomSelectInElement(modal);
});

// Fallback: Initialize on first page load if Livewire hooks don't fire
document.addEventListener('DOMContentLoaded', () => {
	initializeSettings();
	initializeTomSelect();
});
