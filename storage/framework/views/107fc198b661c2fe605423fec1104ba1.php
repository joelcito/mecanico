```blade


<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


    <div class="row g-5 gx-xl-10 mb-5">

        
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#F1416C;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-box fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        <?php echo e($totalProductos ?? 0); ?>

                    </div>

                    <div class="text-white fw-semibold">
                        Productos
                    </div>

                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#4d41f1;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-shopping-cart fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        Bs <?php echo e(number_format($ventasHoy ?? 0, 2)); ?>

                    </div>

                    <div class="text-white fw-semibold">
                        Ventas Hoy
                    </div>

                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#f18241;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-chart-line fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        Bs <?php echo e(number_format($utilidades ?? 0, 2)); ?>

                    </div>

                    <div class="text-white fw-semibold">
                        Utilidades
                    </div>

                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#3e7213;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-users fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        <?php echo e($totalUsuarios ?? 0); ?>

                    </div>

                    <div class="text-white fw-semibold">
                        Usuarios
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="row mb-5">

        
        <div class="col-md-4">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Productos con Stock Bajo
                    </h3>
                </div>

                <div class="card-body">

                </div>

            </div>

        </div>

        
        <div class="col-md-8">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Últimas Ventas
                    </h3>
                </div>

                <div class="card-body table-responsive">

                    <table class="table table-row-bordered">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    
    <div class="row">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Ventas Mensuales
                    </h3>
                </div>

                <div class="card-body">

                    <div id="graficoVentas" style="width:100%; height:400px;">
                    </div>

                </div>

            </div>

        </div>

    </div>


<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js">
    </script>

    <script>

        google.charts.load('current', {
            packages: ['corechart']
        });

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Mes', 'Ventas'],

                <?php $__currentLoopData = $ventasMensuales ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    ['<?php echo e($venta->mes); ?>', <?php echo e($venta->total); ?>],
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    ]);

            var options = {
                title: 'Ventas Mensuales',
                curveType: 'function',
                legend: {
                    position: 'bottom'
                }
            };

            var chart = new google.visualization.LineChart(
                document.getElementById('graficoVentas')
            );

            chart.draw(data, options);
        }

    </script>

<?php $__env->stopSection(); ?>
```

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\mecanico\resources\views/home/inicio.blade.php ENDPATH**/ ?>