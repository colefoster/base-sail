<div style="width: 100%; overflow: hidden;">
    <x-filament::fieldset>
        <x-slot name="label">
            Base Stats
        </x-slot>

        <div style="display: grid; grid-template-columns: 2fr 1fr; align-items: center;">
            <div style="position: relative; height: 250px; width: 100%; overflow: hidden;">
                <canvas
                    style="max-width: 100%; height: 100%;"
                    x-data="{
                        init() {
                            const chartData = @js($this->getChartData());
                            const chartOptions = @js($this->getChartOptions());

                            // Create chart instance (store directly on element, not in Alpine data)
                            const chartInstance = new Chart(this.$el, {
                                type: 'radar',
                                data: chartData,
                                options: chartOptions
                            });

                            // Function to update colors
                            const updateColors = (theme = null) => {
                                // Detect current theme - use event detail if provided, otherwise check DOM
                                let isDark;
                                if (theme !== null) {
                                    // Use the theme from the event
                                    isDark = (theme === 'dark');
                                } else {
                                    // Fallback to checking the DOM class
                                    isDark = document.documentElement.classList.contains('dark');
                                }

                                console.log('Updating chart colors, theme:', theme, 'isDark:', isDark);

                                // Use obvious test colors to verify theme switching
                                const gridColor = !isDark ? '#000000' : '#FFFFFF'; // Green in dark, Red in light
                                const angleLinesColor = !isDark ? '#000000' : '#FFFFFF'; // Cyan in dark, Magenta in light
                                const labelColor = !isDark ? '#000000' : '#FFFFFF'; // Yellow in dark, Blue in light

                                // Update chart colors
                                chartInstance.options.scales.r.grid.color = gridColor;
                                chartInstance.options.scales.r.angleLines.color = angleLinesColor;
                                chartInstance.options.scales.r.pointLabels.color = labelColor;

                                // Redraw chart
                                chartInstance.update();
                            };

                            // Set initial colors
                            updateColors();

                            // Listen for Filament theme change event
                            window.addEventListener('theme-changed', (event) => {
                                console.log('Theme changed event received:', event.detail);
                                updateColors(event.detail);
                            });
                        }
                    }"
                ></canvas>
            </div>

            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">HP</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->hpStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">ATK</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->attackStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">DEF</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->defenseStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">SPA</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->specialAttackStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">SPD</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->specialDefenseStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 500;">SPE</td>
                            <td style="padding: 0.5rem 0; text-align: right;">{{ $record->speedStat ?? 0 }}</td>
                        </tr>
                        <tr style="border-top: 2px solid rgb(229, 231, 235);">
                            <td style="padding: 0.5rem 0; font-weight: 700;">BST</td>
                            <td style="padding: 0.5rem 0; text-align: right; font-weight: 700;">{{ $record->totalBaseStat ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::fieldset>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endassets
