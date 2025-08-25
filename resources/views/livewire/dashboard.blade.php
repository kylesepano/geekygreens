<div>
    {{-- Filters --}}
    <div class="filters">
        <div class="filter-group">
            <label for="shop-select">Shop</label>
            <select id="shop-select" wire:model.prevent="selectedShop">
                <option value="all">All Shops</option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}">{{ $shop->shop_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label for="date-from">Date From</label>
            <input type="date" id="date-from" value="2025-07-20" wire:model.prevent="dateFrom">
        </div>
        <div class="filter-group">
            <label for="date-to">Date To</label>
            <input type="date" id="date-to" value="2025-07-21" wire:model.prevent="dateTo">
        </div>
        <button class="apply-filters" wire:click="applyFilters" onclick="applyFilters()">Apply Filters</button>
    </div>

    {{-- Loading --}}
    <div class="loading" id="loading">
        <div class="spinner"></div>
        <p>Loading dashboard data...</p>
    </div>

    {{-- KPI Cards --}}
    <div class="kpis">
        <div class="kpi-card">
            <div class="kpi-value" id="total-gmv"> ${{ number_format($gmvUsd, 2) }}</div>
            <div class="kpi-label">Total GMV (USD)</div>
            <div class="kpi-change positive"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" id="total-followers">{{ number_format($followers) }}</div>
            <div class="kpi-label">Total Followers</div>
            <div class="kpi-change positive"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" id="avg-ctor">{{ number_format($ctor * 100, 2) }}%</div>
            <div class="kpi-label">Average CTOR</div>
            <div class="kpi-change negative"></div>
        </div>
    </div>
    <div class="data-table">
        <div class="table-header">
            <h3>📊 Detailed Performance Metrics</h3>
        </div>
        <div>
            <table id="metricsTable" class="table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('shop_id')">
                            Shop Name
                            @if ($sortField === 'shop_id')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('date')">
                            Date
                            @if ($sortField === 'date')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('gmv_usd')">
                            GMV (USD)
                            @if ($sortField === 'gmv_usd')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('followers')">
                            Followers
                            @if ($sortField === 'followers')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('ctor')">
                            CTOR
                            @if ($sortField === 'ctor')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginated_metrics as $metric)
                        <tr>
                            <td>{{ $metric->shop->shop_name }}</td>
                            <td>{{ $metric->date }}</td>
                            <td>${{ number_format($metric->gmv_usd, 2) }}</td>
                            <td>{{ $metric->followers }}</td>
                            <td>{{ $metric->ctor * 100 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        <div class="mt-2">
    {{ $paginated_metrics->links('livewire::simple-tailwind') }}
</div>
        </div>

        <div class="dashboard-grid">
            <div class="chart-container ">
                <div class="chart-title">📈 GMV Trend Over Time</div>
                <div class="chart-wrapper" style=" height: 300px; margin: auto;">
                    <canvas id="gmvChart"></canvas>
                </div>
            </div>

            <div class="leaderboard">
                <h3>🏆 Top Performers (GMV)</h3>
                @foreach ($leaderboard as $shop)
                    <div class="leaderboard-item">
                        @if ($loop->first)
                            <div class="leaderboard-rank rank-1">🥇1st</div>
                        @elseif($loop->iteration == 2)
                            <div class="leaderboard-rank rank-2">🥈</div>
                        @elseif($loop->iteration == 3)
                            <div class="leaderboard-rank rank-3">🥉</div>
                        @else
                            <div class="leaderboard-rank rank-3">🏅</div>
                        @endif
                        <div class="leaderboard-info">
                            <div class="leaderboard-name">{{ $shop['shop_name'] }}</div>
                            <div class="leaderboard-score">{{$shop['points']}}</div>
                        </div>
                    </div>
                @endforeach

                <div class="leaderboard-item">

                    <div class="leaderboard-info">
                        <div class="leaderboard-name"></div>
                        <div class="leaderboard-score"></div>
                    </div>
                </div>
                <div class="leaderboard-item">

                    <div class="leaderboard-info">
                        <div class="leaderboard-name"></div>
                        <div class="leaderboard-score"></div>
                    </div>
                </div>
            </div>
        </div>



        <script src="index.js"></script>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.on("refreshChart", ({
            labels,
            datasets
        }) => {
            const ctx = document.getElementById("gmvChart").getContext("2d");


            if (window.gmvChart instanceof Chart) {
                window.gmvChart.destroy();
            }

            window.gmvChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "GMV (USD)"
                            }
                        }
                    }
                }
            });
        });
    });
</script>
