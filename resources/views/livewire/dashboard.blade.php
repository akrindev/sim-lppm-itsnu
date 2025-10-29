<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="align-items-center row g-2">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard
                    </h2>
                    <div class="mt-1 text-muted">
                        Selamat datang, {{ auth()->user()->name }} ({{ $roleName }})
                    </div>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <a href="#" class="btn-outline-primary btn dropdown-toggle" data-bs-toggle="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2 icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                                <path d="M16 6v6l4 2" />
                            </svg>
                            Tahun: {{ $selectedYear }}
                        </a>
                        <div class="dropdown-menu">
                            @foreach ($availableYears as $year)
                                <a href="#" class="dropdown-item {{ $selectedYear == $year ? 'active' : '' }}"
                                    wire:click="$set('selectedYear', {{ $year }})">
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (in_array($roleName, ['superadmin', 'admin lppm', 'admin lppm saintek', 'admin lppm dekabita']))
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Proposal</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_proposals'] ?? 0 }}</div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-1 text-muted icon"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" />
                                            <path d="M12 12c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" />
                                            <path d="M12 12c0 2.21 1.79 4 4 4s4-1.79 4-4-1.79-4-4-4-4 1.79-4 4z" />
                                        </svg>
                                        Penelitian & Pengmas
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Penelitian</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_research'] ?? 0 }}</div>
                                <div class="text-muted">
                                    dari {{ $stats['total_proposals'] ?? 0 }} total proposal
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Pengmas</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_community_service'] ?? 0 }}</div>
                                <div class="text-muted">
                                    dari {{ $stats['total_proposals'] ?? 0 }} total proposal
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Dosen</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_dosen'] ?? 0 }}</div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted">
                                        Pengguna Aktif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Menunggu Review</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['pending_review'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-warning progress-bar" role="progressbar"
                                        style="width: {{ ($stats['pending_review'] / ($stats['total_proposals'] ?? 1)) * 100 }}%">
                                        <span
                                            class="sr-only">{{ ($stats['pending_review'] / ($stats['total_proposals'] ?? 1)) * 100 }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Disetujui</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['approved_proposals'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-success progress-bar" role="progressbar"
                                        style="width: {{ ($stats['approved_proposals'] / ($stats['total_proposals'] ?? 1)) * 100 }}%">
                                        <span
                                            class="sr-only">{{ ($stats['approved_proposals'] / ($stats['total_proposals'] ?? 1)) * 100 }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Ditolak</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['rejected_proposals'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-danger progress-bar" role="progressbar"
                                        style="width: {{ ($stats['rejected_proposals'] / ($stats['total_proposals'] ?? 1)) * 100 }}%">
                                        <span
                                            class="sr-only">{{ ($stats['rejected_proposals'] / ($stats['total_proposals'] ?? 1)) * 100 }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Tingkat Persetujuan</div>
                                </div>
                                <div class="mb-3 h1">
                                    @php
                                        $approvalRate =
                                            $stats['total_proposals'] > 0
                                                ? round(
                                                    ($stats['approved_proposals'] / $stats['total_proposals']) * 100,
                                                    1,
                                                )
                                                : 0;
                                    @endphp
                                    {{ $approvalRate }}%
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $approvalRate }}%">
                                        <span class="sr-only">{{ $approvalRate }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row row-cards" x-data="dashboardCharts()">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Distribution</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="statusDistributionChart"
                                     id="status-distribution-chart-admin"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['status_distribution']) }}"
                                     style="width: 100%; max-width: 300px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Trends ({{ $selectedYear }})</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="monthlyTrendsChart"
                                     id="monthly-trends-chart-admin"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['monthly_trends']) }}"
                                     style="width: 100%; height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function dashboardCharts() {
                        return {
                            init() {
                                this.initStatusDistributionChart();
                                this.initMonthlyTrendsChart();
                                this.initProposalTypesChart();
                            },

                            initStatusDistributionChart() {
                                const element = this.$refs.statusDistributionChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#2fb344', '#e03131', '#fcc419', '#495057', '#0ca678'],
                                });

                                window.initChart('status-distribution-chart-admin', config);
                            },

                            initMonthlyTrendsChart() {
                                const element = this.$refs.monthlyTrendsChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData || !chartData.series || !chartData.categories) return;

                                const config = window.ChartConfig.area({
                                    ...chartData,
                                    height: 300,
                                });

                                window.initChart('monthly-trends-chart-admin', config);
                            },

                            initProposalTypesChart() {
                                const element = document.getElementById('proposal-types-chart');
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#206bc4', '#0ea5e9'],
                                });

                                window.initChart('proposal-types-chart', config);
                            },
                        };
                    }
                </script>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Berdasarkan Jenis</h3>
                            </div>
                            <div class="card-body">
                                <div class="align-items-center mb-4 row">
                                    <div class="col-auto">
                                        <div id="proposal-types-chart" data-chart="proposal-types"
                                            data-chart-data="{{ json_encode($this->getChartDataProperty()['proposal_types']) }}"
                                            style="width: 100%; max-width: 240px; margin: 0 auto;">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="align-items-center row">
                                                <div class="col-auto">
                                                    <div class="bg-primary badge"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">Penelitian</div>
                                                    <div class="text-muted">{{ $proposalsByType['research'] ?? 0 }}
                                                        proposal</div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="text-muted">
                                                        @php
                                                            $totalProposals =
                                                                ($proposalsByType['research'] ?? 0) +
                                                                ($proposalsByType['community_service'] ?? 0);
                                                            $researchPercent =
                                                                $totalProposals > 0
                                                                    ? round(
                                                                        ($proposalsByType['research'] /
                                                                            $totalProposals) *
                                                                            100,
                                                                        1,
                                                                    )
                                                                    : 0;
                                                        @endphp
                                                        {{ $researchPercent }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="align-items-center row">
                                                <div class="col-auto">
                                                    <div class="bg-info badge"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">Pengmas</div>
                                                    <div class="text-muted">
                                                        {{ $proposalsByType['community_service'] ?? 0 }} proposal</div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="text-muted">
                                                        @php
                                                            $csPercent =
                                                                $totalProposals > 0
                                                                    ? round(
                                                                        ($proposalsByType['community_service'] /
                                                                            $totalProposals) *
                                                                            100,
                                                                        1,
                                                                    )
                                                                    : 0;
                                                        @endphp
                                                        {{ $csPercent }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($roleName === 'kepala lppm')
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Proposal</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Menunggu Keputusan</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['pending_review'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-warning progress-bar" role="progressbar"
                                        style="width: {{ ($stats['pending_review'] / ($stats['total_proposals'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Disetujui</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['approved_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Selesai</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['completed_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row row-cards" x-data="kepalaDashboardCharts()">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Distribution</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="statusDistributionChart"
                                     id="status-distribution-chart-kepala"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['status_distribution']) }}"
                                     style="width: 100%; max-width: 300px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Trends ({{ $selectedYear }})</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="monthlyTrendsChart"
                                     id="monthly-trends-chart-kepala"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['monthly_trends']) }}"
                                     style="width: 100%; height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function kepalaDashboardCharts() {
                        return {
                            init() {
                                this.initStatusDistributionChart();
                                this.initMonthlyTrendsChart();
                            },

                            initStatusDistributionChart() {
                                const element = this.$refs.statusDistributionChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#2fb344', '#e03131', '#fcc419', '#495057', '#0ca678'],
                                });

                                window.initChart('status-distribution-chart-kepala', config);
                            },

                            initMonthlyTrendsChart() {
                                const element = this.$refs.monthlyTrendsChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData || !chartData.series || !chartData.categories) return;

                                const config = window.ChartConfig.area({
                                    ...chartData,
                                    height: 300,
                                });

                                window.initChart('monthly-trends-chart-kepala', config);
                            },
                        };
                    }
                </script>
            @elseif($roleName === 'dosen')
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Saya</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['my_proposals'] ?? 0 }}</div>
                                <div class="text-muted">Sebagai Pengaju</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Sebagai Anggota</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['as_team_member'] ?? 0 }}</div>
                                <div class="text-muted">Tim Peneliti/Pengmas</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Menunggu Review</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['pending_review'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-warning progress-bar" role="progressbar"
                                        style="width: {{ ($stats['pending_review'] / ($stats['my_proposals'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Disetujui</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['approved'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-success progress-bar" role="progressbar"
                                        style="width: {{ ($stats['approved'] / ($stats['my_proposals'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row row-cards" x-data="dosenDashboardCharts()">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Proposal Saya</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="statusDistributionChart"
                                     id="status-distribution-chart-dosen"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['status_distribution']) }}"
                                     style="width: 100%; max-width: 300px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Trends ({{ $selectedYear }})</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="monthlyTrendsChart"
                                     id="monthly-trends-chart-dosen"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['monthly_trends']) }}"
                                     style="width: 100%; height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function dosenDashboardCharts() {
                        return {
                            init() {
                                this.initStatusDistributionChart();
                                this.initMonthlyTrendsChart();
                            },

                            initStatusDistributionChart() {
                                const element = this.$refs.statusDistributionChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#2fb344', '#e03131', '#fcc419', '#495057', '#0ca678'],
                                });

                                window.initChart('status-distribution-chart-dosen', config);
                            },

                            initMonthlyTrendsChart() {
                                const element = this.$refs.monthlyTrendsChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData || !chartData.series || !chartData.categories) return;

                                const config = window.ChartConfig.area({
                                    ...chartData,
                                    height: 300,
                                });

                                window.initChart('monthly-trends-chart-dosen', config);
                            },
                        };
                    }
                </script>
            @elseif($roleName === 'reviewer')
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Ditugaskan Review</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['assigned_to_review'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Selesai Review</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['completed_reviews'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-success progress-bar" role="progressbar"
                                        style="width: {{ ($stats['completed_reviews'] / ($stats['assigned_to_review'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Belum Review</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['pending_reviews'] ?? 0 }}</div>
                                <div class="progress progress-sm">
                                    <div class="bg-warning progress-bar" role="progressbar"
                                        style="width: {{ ($stats['pending_reviews'] / ($stats['assigned_to_review'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row row-cards" x-data="reviewerDashboardCharts()">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Review Proposal</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="statusDistributionChart"
                                     id="status-distribution-chart-reviewer"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['status_distribution']) }}"
                                     style="width: 100%; max-width: 300px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Trends ({{ $selectedYear }})</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="monthlyTrendsChart"
                                     id="monthly-trends-chart-reviewer"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['monthly_trends']) }}"
                                     style="width: 100%; height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function reviewerDashboardCharts() {
                        return {
                            init() {
                                this.initStatusDistributionChart();
                                this.initMonthlyTrendsChart();
                            },

                            initStatusDistributionChart() {
                                const element = this.$refs.statusDistributionChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#2fb344', '#e03131', '#fcc419', '#495057', '#0ca678'],
                                });

                                window.initChart('status-distribution-chart-reviewer', config);
                            },

                            initMonthlyTrendsChart() {
                                const element = this.$refs.monthlyTrendsChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData || !chartData.series || !chartData.categories) return;

                                const config = window.ChartConfig.area({
                                    ...chartData,
                                    height: 300,
                                });

                                window.initChart('monthly-trends-chart-reviewer', config);
                            },
                        };
                    }
                </script>
            @elseif(in_array($roleName, ['rektor', 'dekan']))
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Proposal</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Penelitian</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_research'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Pengmas</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_community_service'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Proposal Disetujui</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['approved_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row row-cards" x-data="rektorDashboardCharts()">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Distribution</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="statusDistributionChart"
                                     id="status-distribution-chart-rektor"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['status_distribution']) }}"
                                     style="width: 100%; max-width: 300px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Types</h3>
                            </div>
                            <div class="card-body">
                                <div class="align-items-center mb-4 row">
                                    <div class="col-auto">
                                        <div x-ref="proposalTypesChart"
                                             id="proposal-types-chart-rektor"
                                             data-chart-data="{{ json_encode($this->getChartDataProperty()['proposal_types']) }}"
                                             style="width: 100%; max-width: 240px; margin: 0 auto;">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="align-items-center row">
                                                <div class="col-auto">
                                                    <div class="bg-primary badge"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">Penelitian</div>
                                                    <div class="text-muted">{{ $proposalsByType['research'] ?? 0 }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="text-muted">
                                                        @php
                                                            $totalProposals =
                                                                ($proposalsByType['research'] ?? 0) +
                                                                ($proposalsByType['community_service'] ?? 0);
                                                            $researchPercent =
                                                                $totalProposals > 0
                                                                    ? round(
                                                                        ($proposalsByType['research'] /
                                                                            $totalProposals) *
                                                                            100,
                                                                        1,
                                                                    )
                                                                    : 0;
                                                        @endphp
                                                        {{ $researchPercent }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="align-items-center row">
                                                <div class="col-auto">
                                                    <div class="bg-info badge"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">Pengmas</div>
                                                    <div class="text-muted">
                                                        {{ $proposalsByType['community_service'] ?? 0 }}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="text-muted">
                                                        @php
                                                            $csPercent =
                                                                $totalProposals > 0
                                                                    ? round(
                                                                        ($proposalsByType['community_service'] /
                                                                            $totalProposals) *
                                                                            100,
                                                                        1,
                                                                    )
                                                                    : 0;
                                                        @endphp
                                                        {{ $csPercent }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proposal Trends ({{ $selectedYear }})</h3>
                            </div>
                            <div class="card-body">
                                <div x-ref="monthlyTrendsChart"
                                     id="monthly-trends-chart-rektor"
                                     data-chart-data="{{ json_encode($this->getChartDataProperty()['monthly_trends']) }}"
                                     style="width: 100%; height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function rektorDashboardCharts() {
                        return {
                            init() {
                                this.initStatusDistributionChart();
                                this.initProposalTypesChart();
                                this.initMonthlyTrendsChart();
                            },

                            initStatusDistributionChart() {
                                const element = this.$refs.statusDistributionChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#2fb344', '#e03131', '#fcc419', '#495057', '#0ca678'],
                                });

                                window.initChart('status-distribution-chart-rektor', config);
                            },

                            initProposalTypesChart() {
                                const element = this.$refs.proposalTypesChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData) return;

                                const config = window.ChartConfig.donut({
                                    ...chartData,
                                    height: 240,
                                    colors: ['#206bc4', '#0ea5e9'],
                                });

                                window.initChart('proposal-types-chart-rektor', config);
                            },

                            initMonthlyTrendsChart() {
                                const element = this.$refs.monthlyTrendsChart;
                                const chartData = window.parseChartData(element);

                                if (!chartData || !chartData.series || !chartData.categories) return;

                                const config = window.ChartConfig.area({
                                    ...chartData,
                                    height: 300,
                                });

                                window.initChart('monthly-trends-chart-rektor', config);
                            },
                        };
                    }
                </script>
            @else
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Proposal</div>
                                </div>
                                <div class="mb-3 h1">{{ $stats['total_proposals'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-3 row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Proposal Terbaru</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="card-table table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Judul Proposal</th>
                                        <th>Pengaju</th>
                                        <th>Jenis</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentProposals as $proposal)
                                        <tr>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ $proposal->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <div class="avatar-rounded avatar"
                                                        style="background-image: url({{ $proposal->submitter->profile_picture }})">
                                                    </div>
                                                    <div class="flex-fill ms-2">
                                                        <div class="font-weight-medium">
                                                            {{ $proposal->submitter->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($proposal->detailable_type === 'App\Models\Research')
                                                    <span class="bg-primary me-1 badge">Penelitian</span>
                                                @else
                                                    <span class="bg-info me-1 badge">Pengmas</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($proposal->status === 'approved')
                                                    <span class="bg-success badge">Disetujui</span>
                                                @elseif($proposal->status === 'rejected')
                                                    <span class="bg-danger badge">Ditolak</span>
                                                @elseif($proposal->status === 'submitted')
                                                    <span class="bg-warning badge">Menunggu Review</span>
                                                @elseif($proposal->status === 'reviewed')
                                                    <span class="bg-info badge">Sudah Direview</span>
                                                @elseif($proposal->status === 'completed')
                                                    <span class="bg-success badge">Selesai</span>
                                                @else
                                                    <span
                                                        class="bg-secondary badge">{{ ucfirst($proposal->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $proposal->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-muted text-center">
                                                Belum ada proposal
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/dashboard-charts.js')
</div>
