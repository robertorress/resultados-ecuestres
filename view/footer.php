<footer class="page-footer blue darken-4 center">
    <div class="footer-copyright">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <p>© Copyright <?php echo date('Y'); ?></p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <p> <?php echo get_date(); ?></p>
                </div>
            </div>
        </div>
    </div>
</footer>

</div> <!-- class="container" -->

<!--  Scripts-->
<script defer src="materialize/js/materialize.min.js"></script>
<script defer src="materialize/js/init.js?v=<?php echo RESULTADOS_INIT_JS_VERSION; ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.collapsible');
        var instances = M.Collapsible.init(elems);
    });
</script>
</body>

</html>