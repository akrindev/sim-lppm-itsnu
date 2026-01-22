import "@tabler/core/dist/libs/nouislider/dist/nouislider.min.js";
import TomSelect from "@tabler/core/dist/libs/tom-select/dist/js/tom-select.base.js";
import * as Tabler from "@tabler/core/js/tabler";
import NProgress from "nprogress";
import "./theme-config";

window.tabler = Tabler;
window.bootstrap = Tabler.bootstrap;
// Make TomSelect available globally
window.TomSelect = TomSelect;

// NProgress Configuration
NProgress.configure({ showSpinner: false });
window.NProgress = NProgress;

/**
 * Configuration items for menu and layout settings
 */
const SETTINGS_CONFIG = {
    "menu-position": {
        localStorage: "tablerMenuPosition",
        default: "top",
    },
    "menu-behavior": {
        localStorage: "tablerMenuBehavior",
        default: "sticky",
    },
    "container-layout": {
        localStorage: "tablerContainerLayout",
        default: "boxed",
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
        window.dispatchEvent(new Event("resize"));
    }
}

/**
 * Initialize settings functionality
 */
const initializeSettings = () => {
    const settingsManager = new SettingsManager(SETTINGS_CONFIG);
    const settingsForm = document.querySelector("#offcanvasSettings");

    // Parse URL parameters on load
    settingsManager.parseUrlParams();

    // Setup form if it exists
    if (settingsForm) {
        // Sync form controls with current settings
        settingsManager.syncFormControls(settingsForm);

        // Handle form submission
        settingsForm.addEventListener("submit", (event) => {
            event.preventDefault();

            settingsManager.saveFromForm(settingsForm);

            // Hide offcanvas if bootstrap is available
            if (typeof bootstrap !== "undefined" && bootstrap.Offcanvas) {
                const offcanvas =
                    bootstrap.Offcanvas.getInstance(settingsForm) ||
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
    placeholder: "Pilih opsi...",
    searchField: ["text"],
    valueField: "value",
    labelField: "text",
    copyClassesToDropdown: false,
    dropdownParent: "body",
    controlInput: "<input>",
    render: {
        item: (data, escapeFunc) => {
            if (data.customProperties) {
                return (
                    '<div><span class="dropdown-item-indicator">' +
                    data.customProperties +
                    "</span>" +
                    escapeFunc(data.text) +
                    "</div>"
                );
            }
            return "<div>" + escapeFunc(data.text) + "</div>";
        },
        option: (data, escapeFunc) => {
            if (data.customProperties) {
                return (
                    '<div><span class="dropdown-item-indicator">' +
                    data.customProperties +
                    "</span>" +
                    escapeFunc(data.text) +
                    "</div>"
                );
            }
            return "<div>" + escapeFunc(data.text) + "</div>";
        },
    },
};

/**
 * Alpine.js component for Tom Select with Livewire 3 integration
 * Uses wire:ignore to prevent Livewire from morphing the select element
 */
document.addEventListener("alpine:init", () => {
    Alpine.data("tomSelect", () => ({
        instance: null,

        init() {
            const select = this.$el;

            // Initialize Tom Select
            this.instance = new TomSelect(select, {
                ...TOM_SELECT_CONFIG,
                placeholder:
                    select.getAttribute("placeholder") ||
                    TOM_SELECT_CONFIG.placeholder,
                onChange: (value) => {
                    // Update the underlying select value
                    select.value = value;
                    // Dispatch events for Livewire to pick up
                    select.dispatchEvent(
                        new Event("change", { bubbles: true }),
                    );
                    select.dispatchEvent(new Event("input", { bubbles: true }));
                },
            });

            // Listen for Livewire updates to sync Tom Select
            // This handles when Livewire updates the value programmatically
            this.$watch("$el.value", (value) => {
                if (this.instance && this.instance.getValue() !== value) {
                    this.instance.setValue(value, true);
                }
            });
        },

        destroy() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },
    }));

    /**
     * Alpine.js component for Tom Select with create functionality
     * Allows users to create new options on the fly
     */
    Alpine.data("tomSelectWithCreate", () => ({
        instance: null,

        init() {
            const select = this.$el;

            this.instance = new TomSelect(select, {
                ...TOM_SELECT_CONFIG,
                create: true,
                createOnBlur: true,
                placeholder:
                    select.getAttribute("placeholder") ||
                    TOM_SELECT_CONFIG.placeholder,
                onChange: (value) => {
                    select.value = value;
                    select.dispatchEvent(
                        new Event("change", { bubbles: true }),
                    );
                    select.dispatchEvent(new Event("input", { bubbles: true }));
                },
            });

            this.$watch("$el.value", (value) => {
                if (this.instance && this.instance.getValue() !== value) {
                    this.instance.setValue(value, true);
                }
            });
        },

        destroy() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },
    }));

    /**
     * Alpine.js component for Money/Rupiah Input
     * Real-time masking with cursor position management
     */
    Alpine.data("moneyInput", (index) => ({
        display: "",

        init() {
            this.updateDisplay();
            // Watch for external changes to the Livewire model
            this.$watch(
                `$wire.form.budget_items.${index}.unit_price`,
                (value) => {
                    this.updateDisplay(value);
                },
            );
        },

        updateDisplay(val) {
            val = val || this.$wire.get(`form.budget_items.${index}.unit_price`);
            if (val === "" || val === null || val === undefined) {
                this.display = "";
                return;
            }
            let numericVal = parseInt(val.toString().replace(/[^0-9]/g, ""));
            if (isNaN(numericVal)) {
                this.display = "";
                return;
            }
            this.display = new Intl.NumberFormat("id-ID").format(numericVal);
        },

        handleFocus() {
            this.$nextTick(() => {
                this.$refs.input.select();
            });
        },

        handleInput(e) {
            let input = e.target;
            let rawValue = input.value.replace(/[^0-9]/g, "");

            // Handle empty input
            if (rawValue === "") {
                this.display = "";
                this.$wire.set(`form.budget_items.${index}.unit_price`, 0);
                this.$wire.calculateTotal(index);
                return;
            }

            // Keep track of cursor position from the END
            // This is more reliable when dots are inserted/removed
            let selectionEnd = input.selectionEnd;
            let lengthBefore = input.value.length;
            let offsetFromEnd = lengthBefore - selectionEnd;

            // Format the raw value
            let numericVal = parseInt(rawValue);
            let formattedValue = new Intl.NumberFormat("id-ID").format(
                numericVal,
            );

            // Update state
            this.display = formattedValue;
            this.$wire.set(
                `form.budget_items.${index}.unit_price`,
                numericVal,
                false,
            );
            this.$wire.calculateTotal(index);

            // Restore cursor position
            this.$nextTick(() => {
                let lengthAfter = this.display.length;
                let newPosition = lengthAfter - offsetFromEnd;
                input.setSelectionRange(newPosition, newPosition);
            });
        },
    }));
});

// Fallback: Initialize on page navigation (e.g., wire:navigate)
document.addEventListener("livewire:navigated", () => {
    initializeSettings();
});

// Initialize Tom Select specifically for modal content when modals are shown
document.addEventListener("shown.bs.modal", (event) => {
    const modal = event.target;
    const selects = modal.querySelectorAll("select[x-data*='tomSelect']");
    // Alpine will handle initialization automatically
});

// Fallback: Initialize on first page load
document.addEventListener("DOMContentLoaded", () => {
    initializeSettings();
});

// Livewire Global Progress Bar
document.addEventListener("livewire:init", () => {
    Livewire.hook("request", ({ fail, respond, succeed }) => {
        NProgress.start();

        respond(() => {
            NProgress.done();
        });

        fail(() => {
            NProgress.done();
        });
    });
});
