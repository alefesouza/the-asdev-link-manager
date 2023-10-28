<?php

require_once 'init.php';

if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
	header( 'WWW-Authenticate: Basic realm="My Realm"' );
	?>
	<script>
		window.location.href = 'https://viajarcomale.com';
	</script>
	<?php

	exit;
} elseif ( $_SERVER['PHP_AUTH_USER'] !== $username || $_SERVER['PHP_AUTH_PW'] !== $password ) {
	header( 'location:https://viajarcomale.com' );
	exit;
}

if ( isset( $_POST['submit'] ) ) {
	$link         = filter_var( $_POST['link'], FILTER_SANITIZE_URL );
	$short_link   = htmlspecialchars( $_POST['short_link'] );
	$ios_link     = filter_var( $_POST['ios_link'], FILTER_SANITIZE_URL );
	$android_link = filter_var( $_POST['android_link'], FILTER_SANITIZE_URL );
	$clicks       = filter_var( $_POST['clicks'], FILTER_SANITIZE_NUMBER_INT );

	if ( strpos( $link, 'youtube.com' ) !== false ) {
		$queries = [];
		parse_str( parse_url( $link )['query'], $queries );
		$video_id = $queries['v'];

		if ( ! empty( $video_id ) ) {
			if ( empty( $ios_link ) ) {
				$ios_link = 'vnd.youtube://www.youtube.com/watch?v=' . $video_id . '&sub_confirmation=1';
			}

			if ( empty( $short_link ) ) {
				$short_link = $video_id;
			}
		}
	}

	if ( strpos( $link, 'youtu.be' ) !== false ) {
		$url_parts = explode( '/', $link );
		$video_id  = $url_parts[ count( $url_parts ) - 1 ];
		$video_id  = explode( '?', $video_id )[0];

		if ( ! empty( $video_id ) ) {
			if ( empty( $ios_link ) ) {
				$ios_link = 'vnd.youtube://www.youtube.com/watch?v=' . $video_id . '&sub_confirmation=1';
			}

			if ( empty( $short_link ) ) {
				$short_link = $video_id;
			}

			$link = 'https://youtube.com/watch?v=' . $video_id;
		}
	}

	$query = $db->prepare( 'SELECT * FROM links WHERE short_link=:short_link' );
	$query->bindValue( ':short_link', $short_link, PDO::PARAM_STR );
	$query->execute();

	$row = $query->fetch( PDO::FETCH_ASSOC );

	if ( $query->rowCount() === 0 ) {
		$query = $db->prepare( 'INSERT INTO links (link, short_link, ios_link, android_link, clicks) VALUES (:link, :short_link, :ios_link, :android_link, :clicks)' );
	} else {
		$query = $db->prepare( 'UPDATE links SET link=:link, short_link=:short_link, ios_link=:ios_link, android_link=:android_link, clicks=:clicks WHERE short_link=:short_link' );
	}

	$query->bindValue( ':link', $link, PDO::PARAM_STR );
	$query->bindValue( ':short_link', $short_link, PDO::PARAM_STR );
	$query->bindValue( ':ios_link', $ios_link, PDO::PARAM_STR );
	$query->bindValue( ':android_link', $android_link, PDO::PARAM_STR );
	$query->bindValue( ':clicks', $clicks, PDO::PARAM_INT );

	$query->execute();
}

$list = $db->prepare( 'SELECT * FROM links ORDER BY id DESC' );
$list->execute();

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>The AS.dev Link Manager</title>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
			rel="stylesheet"
			integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
			crossorigin="anonymous"
		/>
	</head>
	<body>
	    <nav class="navbar navbar-expand-lg bg-body-tertiary">
          <div class="container">
            <a class="navbar-brand" href="#">
              <img src="/favicon.ico" alt="Logo" width="24" height="24" class="d-inline-block align-text-top">
              The AS.dev Link Manager
            </a>
          </div>
        </nav>
		<div class="container">
			<form class="row g-3 pt-3 pb-1" method="POST" action="">
				<div class="col-6">
					<label for="link">Link</label>
					<input
						type="url"
						class="form-control"
						id="link"
						name="link"
						placeholder="Link"
						value=""
					/>
				</div>
				<div class="col-6">
					<label for="short_link">Short Link</label>
					<input
						type="text"
						class="form-control"
						id="short_link"
						name="short_link"
						placeholder="Short link"
						value=""
					/>
				</div>
				<div class="col-6">
					<label for="ios_link">iOS Link</label>
					<input
						type="url"
						class="form-control"
						id="ios_link"
						name="ios_link"
						placeholder="iOS Link"
						value=""
					/>
				</div>
				<div class="col-6">
					<label for="android_link">Android Link</label>
					<input
						type="url"
						class="form-control"
						id="android_link"
						name="android_link"
						placeholder="Android Link"
						value=""
					/>
				</div>
				<div class="col-12">
					<label for="clicks">Clicks</label>
					<input
						type="number"
						class="form-control"
						id="clicks"
						name="clicks"
						placeholder="Clicks"
						value="0"
					/>
				</div>
				<div class="col-auto">
					<button
						type="submit"
						name="submit"
						class="btn btn-primary mb-3"
					>
						Submit
					</button>
				</div>
			</form>

			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th scope="col">ID</th>
						<th scope="col">Link</th>
						<th scope="col">Clicks</th>
						<th scope="col">Short Link</th>
						<th scope="col">iOS Link</th>
						<th scope="col">Android Link</th>
					</tr>
				</thead>
				<tbody>
					<?php while ( $row = $list->fetch( PDO::FETCH_ASSOC ) ) { ?>
					<tr>
						<th scope="row"><?php echo filter_var( $row['id'], FILTER_SANITIZE_NUMBER_INT ); ?></th>
						<td><?php echo filter_var( $row['link'], FILTER_SANITIZE_URL ); ?></td>
						<td><?php echo filter_var( $row['clicks'], FILTER_SANITIZE_NUMBER_INT ); ?></td>
						<td><?php echo htmlspecialchars( $row['short_link'] ); ?></td>
						<td><?php echo filter_var( $row['ios_link'], FILTER_SANITIZE_URL ); ?></td>
						<td><?php echo filter_var( $row['android_link'], FILTER_SANITIZE_URL ); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<script
			src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
			crossorigin="anonymous"
		></script>
	</body>
</html>
