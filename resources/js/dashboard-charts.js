/**
 * Dashboard Charts Base Configuration
 * Centralized ApexCharts configuration for all dashboard charts
 */
import ApexCharts from "@tabler/core/dist/libs/apexcharts/dist/apexcharts.min.js";

window.ApexCharts = ApexCharts;

/**
 * Base chart configuration factory
 */
window.ChartConfig = {
	/**
	 * Create base chart options
	 */
	base() {
		return {
			chart: {
				fontFamily: "inherit",
				animations: {
					enabled: true,
				},
			},
			grid: {
				strokeDashArray: 4,
			},
			tooltip: {
				theme: "dark",
			},
		};
	},

	/**
	 * Donut chart configuration
	 */
	donut(data = {}) {
		return {
			chart: {
				...this.base().chart,
				type: "donut",
				height: data.height || 240,
				sparkline: {
					enabled: true,
				},
			},
			series: data.series || [],
			labels: data.labels || [],
			colors: data.colors || [
				"#206bc4",
				"#0ea5e9",
				"#2fb344",
				"#e03131",
				"#fcc419",
			],
			tooltip: {
				...this.base().tooltip,
				y: {
					formatter: (val) => `${val} proposal${val > 1 ? "s" : ""}`,
				},
			},
			legend: {
				show: true,
				position: data.legendPosition || "bottom",
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
							height: data.responsiveHeight || 200,
						},
						legend: {
							position: "bottom",
						},
					},
				},
			],
		};
	},

	/**
	 * Area chart configuration
	 */
	area(data = {}) {
		return {
			chart: {
				...this.base().chart,
				type: "area",
				height: data.height || 300,
				sparkline: {
					enabled: false,
				},
			},
			series: data.series || [],
			xaxis: {
				categories: data.categories || [],
			},
			colors: data.colors || ["#206bc4"],
			tooltip: {
				...this.base().tooltip,
				y: {
					formatter: (val) => `${val} proposal${val > 1 ? "s" : ""}`,
				},
			},
			fill: {
				type: "gradient",
				gradient: {
					shadeIntensity: 1,
					opacityFrom: 0.7,
					opacityTo: 0.3,
					stops: [0, 90, 100],
				},
			},
			stroke: {
				curve: "smooth",
				width: 2,
			},
			legend: {
				show: false,
			},
			responsive: [
				{
					breakpoint: 768,
					options: {
						chart: {
							height: data.responsiveHeight || 250,
						},
					},
				},
			],
		};
	},

	/**
	 * Bar chart configuration
	 */
	bar(data = {}) {
		return {
			chart: {
				...this.base().chart,
				type: "bar",
				height: data.height || 300,
				sparkline: {
					enabled: false,
				},
			},
			series: data.series || [],
			xaxis: {
				categories: data.categories || [],
			},
			colors: data.colors || ["#206bc4"],
			tooltip: {
				...this.base().tooltip,
				y: {
					formatter: (val) => `${val} proposal${val > 1 ? "s" : ""}`,
				},
			},
			plotOptions: {
				bar: {
					horizontal: data.horizontal || false,
					columnWidth: data.columnWidth || "55%",
					borderRadius: data.borderRadius || 0,
				},
			},
			legend: {
				show: true,
				position: data.legendPosition || "top",
			},
			responsive: [
				{
					breakpoint: 768,
					options: {
						chart: {
							height: data.responsiveHeight || 250,
						},
					},
				},
			],
		};
	},

	/**
	 * Line chart configuration
	 */
	line(data = {}) {
		return {
			chart: {
				...this.base().chart,
				type: "line",
				height: data.height || 300,
				sparkline: {
					enabled: false,
				},
			},
			series: data.series || [],
			xaxis: {
				categories: data.categories || [],
			},
			colors: data.colors || ["#206bc4"],
			tooltip: {
				...this.base().tooltip,
				y: {
					formatter: (val) => `${val} proposal${val > 1 ? "s" : ""}`,
				},
			},
			stroke: {
				curve: data.curve || "smooth",
				width: data.width || 2,
			},
			legend: {
				show: true,
				position: data.legendPosition || "top",
			},
			responsive: [
				{
					breakpoint: 768,
					options: {
						chart: {
							height: data.responsiveHeight || 250,
						},
					},
				},
			],
		};
	},

	/**
	 * Pie chart configuration
	 */
	pie(data = {}) {
		return {
			chart: {
				...this.base().chart,
				type: "pie",
				height: data.height || 240,
				sparkline: {
					enabled: true,
				},
			},
			series: data.series || [],
			labels: data.labels || [],
			colors: data.colors || [
				"#206bc4",
				"#0ea5e9",
				"#2fb344",
				"#e03131",
				"#fcc419",
			],
			tooltip: {
				...this.base().tooltip,
				y: {
					formatter: (val) => `${val} proposal${val > 1 ? "s" : ""}`,
				},
			},
			legend: {
				show: true,
				position: data.legendPosition || "bottom",
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
							height: data.responsiveHeight || 200,
						},
						legend: {
							position: "bottom",
						},
					},
				},
			],
		};
	},
};

/**
 * Chart initialization helper
 */
window.initChart = (elementId, config) => {
	if (!window.ApexCharts) {
		console.error("ApexCharts not available");
		return null;
	}

	const element = document.getElementById(elementId);
	if (!element) {
		console.error(`Chart element with id "${elementId}" not found`);
		return null;
	}

	const chart = new ApexCharts(element, config);
	chart.render();

	return chart;
};

/**
 * Chart destroy helper
 */
window.destroyChart = (chartInstance) => {
	if (chartInstance) {
		chartInstance.destroy();
	}
};

/**
 * Chart update helper
 */
window.updateChart = (chartInstance, data) => {
	if (chartInstance) {
		chartInstance.updateSeries(data.series, data.labels);
	}
};

/**
 * Parse chart data from element
 */
window.parseChartData = (element) => {
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
};
