/**
 * Dashboard Charts Initialization
 * Handles all ApexCharts for the dashboard component
 */
import '@tabler/core/dist/libs/apexcharts/dist/apexcharts.min.js';

class DashboardCharts {
	constructor() {
		this.charts = {};
		this.init();
	}

	init() {
		// Wait for DOM to be ready
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", () => this.setupCharts());
		} else {
			this.setupCharts();
		}

		// Listen for Livewire events
		this.setupLivewireListeners();
	}

	setupCharts() {
		this.initProposalTypeCharts();
		this.initStatusDistributionCharts();
	}

	setupLivewireListeners() {
		// Listen for Livewire updates
		document.addEventListener("livewire:init", () => {
			Livewire.hook("component.init", ({ component }) => {
				this.handleComponentInit(component);
			});

			Livewire.hook("morphed", ({ component }) => {
				this.handleComponentMorphed(component);
			});
		});

		document.addEventListener("livewire:navigated", () => {
			setTimeout(() => this.setupCharts(), 100);
		});
	}

	handleComponentInit(component) {
		if (component.el.closest("[wire\\:id]")) {
			setTimeout(() => this.setupCharts(), 100);
		}
	}

	handleComponentMorphed(component) {
		// Reinitialize charts after Livewire updates
		setTimeout(() => {
			this.destroyAllCharts();
			this.setupCharts();
		}, 100);
	}

	initProposalTypeCharts() {
		const chartElements = document.querySelectorAll(
			'[data-chart="proposal-types"]',
		);
		chartElements.forEach((element, index) => {
			const chartId = element.id || `proposal-types-chart-${index}`;
			element.id = chartId;

			if (!window.ApexCharts) {
				console.warn("ApexCharts not available");
				return;
			}

			const data = this.getChartData(element);
			if (!data) return;

			this.charts[chartId] = new ApexCharts(element, {
				chart: {
					type: "donut",
					fontFamily: "inherit",
					height: 240,
					sparkline: {
						enabled: true,
					},
					animations: {
						enabled: true,
					},
				},
				series: data.series,
				labels: data.labels,
				tooltip: {
					theme: "dark",
					y: {
						formatter: (val) => val + " proposal" + (val > 1 ? "s" : ""),
					},
				},
				grid: {
					strokeDashArray: 4,
				},
				colors: data.colors || [
					"#206bc4", // Primary blue for research
					"#0ea5e9", // Info blue for community service
				],
				legend: {
					show: true,
					position: "bottom",
					offsetY: 12,
					markers: {
						width: 10,
						height: 10,
						radius: 100,
					},
					itemMargin: {
						horizontal: 8,
						vertical: 8,
					},
				},
				responsive: [
					{
						breakpoint: 480,
						options: {
							chart: {
								height: 200,
							},
							legend: {
								position: "bottom",
							},
						},
					},
				],
			});

			this.charts[chartId].render();
		});
	}

	initStatusDistributionCharts() {
		const chartElements = document.querySelectorAll(
			'[data-chart="status-distribution"]',
		);
		chartElements.forEach((element, index) => {
			const chartId = element.id || `status-distribution-chart-${index}`;
			element.id = chartId;

			if (!window.ApexCharts) {
				console.warn("ApexCharts not available");
				return;
			}

			const data = this.getChartData(element);
			if (!data) return;

			this.charts[chartId] = new ApexCharts(element, {
				chart: {
					type: "donut",
					fontFamily: "inherit",
					height: 240,
					sparkline: {
						enabled: true,
					},
					animations: {
						enabled: true,
					},
				},
				series: data.series,
				labels: data.labels,
				tooltip: {
					theme: "dark",
					y: {
						formatter: (val) => val + " proposal" + (val > 1 ? "s" : ""),
					},
				},
				grid: {
					strokeDashArray: 4,
				},
				colors: data.colors || [
					"#2fb344", // Success green
					"#e03131", // Danger red
					"#fcc419", // Warning yellow
					"#495057", // Secondary gray
					"#0ca678", // Teal
				],
				legend: {
					show: true,
					position: "bottom",
					offsetY: 12,
					markers: {
						width: 10,
						height: 10,
						radius: 100,
					},
					itemMargin: {
						horizontal: 8,
						vertical: 8,
					},
				},
				responsive: [
					{
						breakpoint: 480,
						options: {
							chart: {
								height: 200,
							},
							legend: {
								position: "bottom",
							},
						},
					},
				],
			});

			this.charts[chartId].render();
		});
	}

	getChartData(element) {
		try {
			const dataAttribute = element.getAttribute("data-chart-data");
			if (!dataAttribute) {
				console.warn("No chart data found");
				return null;
			}

			const jsonData = JSON.parse(dataAttribute);
			return jsonData;
		} catch (error) {
			console.error("Error parsing chart data:", error);
			return null;
		}
	}

	destroyAllCharts() {
		Object.keys(this.charts).forEach((chartId) => {
			if (this.charts[chartId]) {
				this.charts[chartId].destroy();
				delete this.charts[chartId];
			}
		});
	}

	updateChart(chartId, data) {
		if (this.charts[chartId]) {
			this.charts[chartId].updateSeries(data.series, data.labels);
		}
	}
}

// Initialize dashboard charts
window.dashboardCharts = new DashboardCharts();
