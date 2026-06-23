<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($id && $status) {
    $result = updateOrderStatus($id, $status);
    $_SESSION['success'] = $result ? 'Statut mis à jour avec succès' : 'Erreur lors de la mise à jour';
}

header('Location: order_detail.php?id=' . $id);
exit();
?>