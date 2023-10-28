<?php

if ( empty( $_GET['short_link'] ) ) {
	header( 'location:https://viajarcomale.com' );
	exit;
}

require_once 'init.php';

$short_link = htmlspecialchars( $_GET['short_link'] );

$query = $db->prepare( 'SELECT * FROM links WHERE short_link=:short_link' );
$query->bindValue( ':short_link', $short_link, PDO::PARAM_STR );
$query->execute();

$row = $query->fetch( PDO::FETCH_ASSOC );

if ( $query->rowCount() === 0 ) {
	header( 'location:https://viajarcomale.com' );
	exit;
}

$query = $db->prepare( 'UPDATE links SET clicks=clicks+1 WHERE short_link=:short_link' );
$query->bindValue( ':short_link', $short_link, PDO::PARAM_STR );
$query->execute();

$isiOS     = strpos( $_SERVER['HTTP_USER_AGENT'], 'iPod' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) !== false;
$isAndroid = stripos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false;

$link         = filter_var( $row['link'], FILTER_SANITIZE_URL );
$ios_link     = filter_var( $row['ios_link'], FILTER_SANITIZE_URL );
$android_link = filter_var( $row['android_link'], FILTER_SANITIZE_URL );

if ( empty( $row['ios_link'] ) || ( ! $isiOS && ! $isAndroid ) ) {
	header( 'location:' . $row['link'] );
	exit;
}

?>
<script>
	window.location.replace('<?php echo $isAndroid && ! empty( $android_link ) ? $android_link : $ios_link; ?>');

	setTimeout(() => {
		window.location.href = '<?php echo $link; ?>';
	}, 500);
</script>
