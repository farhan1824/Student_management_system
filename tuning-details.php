<div class="ecars-tuning-selector">
    <select id="ecars-tuning-brand-select" disabled>
        <option value="">Select brand</option>
    </select>
    <select id="ecars-tuning-model-select" disabled>
        <option value="">Select model</option>
    </select>
    <select id="ecars-tuning-generation-select" disabled>
        <option value="">Select generation</option>
    </select>
    <select id="ecars-tuning-engine-select" disabled>
        <option value="">Select engine</option>
    </select>
    <button id="ecars-tuning-show-details">Show Tuning Details</button>
</div>


<h2><?php echo ($data->title); ?></h2>

<div class="ecars-tuning-details">

    <section class="chart-wrapper" aria-label="Performance Charts">
        <!-- POWER HP -->
        <article class="chart-box" aria-labelledby="power-title">
            <div id="power_chart" role="img" aria-describedby="power-desc"></div>
            <div class="stats">
                <strong id="power-title">POWER HP</strong>
                <div class="stats-row">
                    <span>Original</span><span><?php echo (int) $data->original_hp; ?> <small>hp</small></span>
                </div>
                <div class="stats-row">
                    <span>Dyno Chiptuning</span>
                    <span class="highlight"><?php echo (int) $data->tuned_hp; ?> <small>hp</small></span>
                </div>
                <div class="stats-row difference">
                    <strong>Difference
                        <span class="info-icon" title="Difference between Dyno Chiptuning and Original power">i</span></strong>
                    <span><?php echo (int) $data->tuned_hp - (int) $data->original_hp; ?> <small>hp</small></span>
                </div>
            </div>
        </article>

        <!-- TORQUE NM -->
        <article class="chart-box" aria-labelledby="torque-title">
            <div id="torque_chart" role="img" aria-describedby="torque-desc"></div>
            <div class="stats">
                <strong id="torque-title">TORQUE NM</strong>
                <div class="stats-row">
                    <span>Original</span><span><?php echo (int) $data->original_torque; ?> <small>Nm</small></span>
                </div>
                <div class="stats-row">
                    <span>Dyno Chiptuning</span>
                    <span class="highlight"><?php echo (int) $data->tuned_torque; ?> <small>Nm</small></span>
                </div>
                <div class="stats-row difference">
                    <strong>Difference
                        <span class="info-icon" title="Difference between Dyno Chiptuning and Original torque">i</span></strong>
                    <span><?php echo (int) $data->tuned_torque - (int) $data->original_torque; ?> <small>Nm</small></span>
                </div>
            </div>
        </article>
    </section>

    <section class="dropdown-wrapper">
        <!-- ENGINE SPECIFICATIONS -->
        <div class="dropdown" id="engineSpecsDropdown">
            <div class="dropdown-header">ENGINE SPECIFICATIONS<span class="dropdown-icon">▼</span></div>
            <div class="dropdown-content" aria-hidden="true">
                <div>
                    <div class="spec-row"><span>Type of fuel</span><strong><?php echo ($data->fuel_type); ?></strong></div>
                    <div class="spec-row"><span>Method</span><strong><?php echo ($data->method); ?></strong></div>
                    <div class="spec-row"><span>Tuning type</span><strong><?php echo ($data->tuning_type); ?></strong></div>
                    <div class="spec-row"><span>Cylinder content</span><strong><?php echo (int) $data->cylinder_content; ?> CC</strong></div>
                    <div class="spec-row"><span>Engine ECU</span><strong><?php echo ($data->engine_ecu); ?></strong></div>
                </div>
                <div>
                    <div class="spec-row"><span>Compression ratio</span><strong><?php echo ($data->compression_ratio); ?> : 1</strong></div>
                    <div class="spec-row"><span>Bore X stroke</span><strong><?php echo ($data->bore_x_stroke); ?></strong></div>
                    <div class="spec-row"><span>Engine number</span><strong><?php echo ($data->engine_number); ?></strong></div>
                </div>
            </div>
        </div>

        <!-- READ METHODS -->
        <div class="dropdown" id="readMethodsDropdown">
            <div class="dropdown-header">READ METHODS<span class="dropdown-icon">▼</span></div>
            <div class="dropdown-content" aria-hidden="true">
                <div class="method-grid">
                    <?php foreach (explode(',', $data->read_methods) as $read_method) : ?>
                        <div class="method-text"><span><?php echo (trim($read_method)); ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ADDITIONAL OPTIONS -->
        <div class="dropdown" id="additionalOptionsDropdown">
            <div class="dropdown-header">ADDITIONAL OPTIONS<span class="dropdown-icon">▼</span></div>
            <div class="dropdown-content" aria-hidden="true">
                <div class="method-grid">
                    <?php foreach (explode(',', $data->additional_options) as $additional_option) : ?>
                        <div class="option-card" role="button">
                            <span class="info-icon">i</span>
                            <strong><?php echo (trim($additional_option)); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    const wrapper = document.querySelector(".dropdown-wrapper");
    if (wrapper) {
        wrapper.addEventListener("click", (e) => {
            const header = e.target.closest(".dropdown-header");
            if (!header) return;
            const dropdown = header.parentElement;

            // Close other dropdowns
            wrapper.querySelectorAll(".dropdown").forEach((d) => {
                if (d !== dropdown) {
                    d.classList.remove("open");
                    d.querySelector(".dropdown-content").setAttribute("aria-hidden", "true");
                }
            });

            // Toggle current dropdown
            const isOpen = dropdown.classList.toggle("open");
            dropdown.querySelector(".dropdown-content").setAttribute("aria-hidden", !isOpen);
        });
    }

    google.charts.load("current", {
        packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        const chartsConfig = [{
                containerId: "power_chart",
                data: [
                    ["Original", <?php echo (int) $data->original_hp; ?>, "#2d3e50", customTooltip("Original", "<?php echo (int) $data->original_hp; ?> hp")],
                    ["Stage 1", <?php echo (int) $data->tuned_hp; ?>, "#ff3b3b", customTooltip("Stage 1", "<?php echo (int) $data->tuned_hp; ?> hp")],
                ],
            },
            {
                containerId: "torque_chart",
                data: [
                    ["Original", <?php echo (int) $data->original_torque; ?>, "#2d3e50", customTooltip("Original", "<?php echo (int) $data->original_torque; ?> Nm")],
                    ["Stage 1", <?php echo (int) $data->tuned_torque; ?>, "#ff3b3b", customTooltip("Stage 1", "<?php echo (int) $data->tuned_torque; ?> Nm")],
                ],
            },
        ];

        chartsConfig.forEach(({
            containerId,
            data
        }) => {
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn("string", "Type");
            dataTable.addColumn("number", "Value");
            dataTable.addColumn({
                type: "string",
                role: "style"
            });
            dataTable.addColumn({
                type: "string",
                role: "tooltip",
                p: {
                    html: true
                }
            });
            dataTable.addRows(data);

            const options = {
                backgroundColor: "#111",
                legend: {
                    position: "none"
                },
                hAxis: {
                    textStyle: {
                        color: "#fff"
                    },
                    baselineColor: "transparent"
                },
                vAxis: {
                    textStyle: {
                        color: "#fff"
                    },
                    gridlines: {
                        color: "#333"
                    },
                    baselineColor: "transparent"
                },
                tooltip: {
                    isHtml: true
                },
                chartArea: {
                    left: 40,
                    right: 20,
                    top: 20,
                    bottom: 40
                },
                bar: {
                    groupWidth: "60%"
                },
            };

            const chart = new google.visualization.ColumnChart(document.getElementById(containerId));
            chart.draw(dataTable, options);
        });
    }

    function customTooltip(title, value) {
        return `<div class="custom-tooltip" style="padding:1px 15px; font-size:14px; font-weight:bold;">${title}<br>${value}</div>`;
    }

    window.addEventListener("resize", drawCharts);
</script>