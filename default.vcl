vcl 4.0;
backend default {
    .host = "banshee";
    .port = "80";
}
sub vcl_recv {
}
sub vcl_backend_response {
}
sub vcl_deliver {
    unset resp.http.X-Powered-By;
    unset resp.http.X-Varnish;
    unset resp.http.via;
    unset resp.http.age;
}
