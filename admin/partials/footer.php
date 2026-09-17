    </div><!-- end main-wrapper -->
</div><!-- end layout -->
<script>
// Auto-hide alerts after 4 seconds
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity 0.4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 4000);
});
</script>
</body>
</html>
