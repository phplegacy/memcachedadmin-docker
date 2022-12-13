<div class="breadcrumbs">
    <a href="/">Home</a> > <a href="/data/?server=<?php echo htmlspecialchars($requestServer, ENT_QUOTES); ?>">Data</a> > <?php echo htmlspecialchars($key); ?>
</div>

<?php if (isset($data)) { ?>
    <table border="1">
        <tr>
            <td>Key:</td>
            <td><?php echo htmlspecialchars($key); ?></td>
        </tr>
        <tr>
            <td>Data:</td>
            <td><?php echo htmlspecialchars($data); ?></td>
        </tr>
    </table>

<?php } ?>
