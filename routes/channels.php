<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público de notificaciones del POS / inventario
Broadcast::channel('pos-canal', function () {
    return true;
});
