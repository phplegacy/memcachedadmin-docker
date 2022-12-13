<?php if (isset($keys)) { ?>
    <table border="1">
        <tr>
            <th>Key</th>
            <th>Size (bytes)</th>
            <th>TTL</th>
        </tr>
        <?php foreach ($keys as $key) { ?>
            <tr>
                <td><a href="/key/?name=<?php echo htmlspecialchars($key['name'], ENT_QUOTES); ?>&server=<?php echo htmlspecialchars($requestServer, ENT_QUOTES); ?>"><?php echo htmlspecialchars($key['name']); ?></a></td>
                <td align="right"><?php echo $key['size']; ?></td>
                <td>
                    <?php if ($key['ttl']) { ?>
                        <?php echo date('Y-m-d H:i:s', $key['ttl']); ?>
                    <?php } else { ?>
                            ∞
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
