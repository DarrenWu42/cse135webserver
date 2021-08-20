#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#include "httpd.h" 
#include "http_config.h"

module AP_MODULE_DECLARE_DATA   c_programs = { 
    STANDARD20_MODULE_STUFF,
    NULL, /* Per-directory configuration handler */
    NULL,  /* Merge handler for per-directory configurations */
    NULL, /* Per-server configuration handler */
    NULL,  /* Merge handler for per-server configurations */
    NULL,      /* Any directives we may have for httpd */
    register_hooks   /* Our hook registering function */
};

typedef void (*FunctionCallback)(request_rec);
FunctionCallback functions[] = {&destroy_session, &env, &general_request_echo, &get_echo,
                                &hello_html, &hello_json, &post_echo, &sessions_1, &sessions_2};

static void register_hooks(apr_pool_t *pool){
    //ap_hook_handler(page_caller, NULL, NULL, APR_HOOK_LAST);
    ///*
    ap_hook_handler(destroy_session, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(env, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(general_request_echo, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(get_echo, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(hello_html, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(hello_json, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(post_echo, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(sessions_1, NULL, NULL, APR_HOOK_LAST);
    ap_hook_handler(sessions_2, NULL, NULL, APR_HOOK_LAST);
    //*/
}

static int page_caller(request_rec *r){
    //if (!r->handler || strcmp(r->handler, "page-caller-handler")) return (DECLINED);

    char* filename;
    char* filename_prefix;
    int function_index;

    filename = apr_pstrdup(r->pool, r->filename);
    filename[strlen(filename)-3] = 0; // Cut off the last 3 characters (.cm)

    if(strcmp(filename, "destroy_session") == 0)
        return functions[0](r);
    else if(strcmp(filename, "env") == 0)
        return functions[1](r);
    else if(strcmp(filename, "general_request_echo") == 0)
        return functions[2](r);
    else if(strcmp(filename, "get_echo") == 0)
        return functions[3](r);
    else if(strcmp(filename, "hello_html") == 0)
        return functions[4](r);
    else if(strcmp(filename, "hello_json") == 0)
        return functions[5](r);
    else if(strcmp(filename, "post_echo") == 0)
        return functions[6](r);
    else if(strcmp(filename, "sessions_1") == 0)
        return functions[7](r);
    else if(strcmp(filename, "sessions_2") == 0)
        return functions[8](r);
    else
        return HTTP_NOT_FOUND;
}

static int destroy_session(request_rec *r){
    if (!r->handler || strcmp(r->handler, "destroy-session-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int env(request_rec *r){
    if (!r->handler || strcmp(r->handler, "env-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int general_request_echo(request_rec *r){
    if (!r->handler || strcmp(r->handler, "general-request-echo-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int get_echo(request_rec *r){
    if (!r->handler || strcmp(r->handler, "get-echo-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int hello_html(request_rec *r){
    if (!r->handler || strcmp(r->handler, "hello-html-handler")) return (DECLINED);
    
    time_t t;
    time(&t);

    ap_set_content_type(r, "text/html");
    ap_rprintf(r, "Cache-Control: no-cache\n\n");

    ap_rprintf(r, "<html><head><title>Hello, C!</title></head>\
        <body><h1 align=center>Hello, C!</h1>\
        <hr/>\n");

    ap_rprintf(r, "Hello, World!<br/>\n");
    ap_rprintf(r, "This program was generated at: %s\n<br/>", ctime(&t));
    ap_rprintf(r, "This program filename is: %s\n<br/>", r->filename);
    ap_rprintf(r, "Your current IP address is: %s<br/>", r->useragent_ip);
    
    // Print HTML footer
    ap_rprintf("</body></html>");

    return OK;
}

static int hello_json(request_rec *r){
    if (!r->handler || strcmp(r->handler, "hello-json-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int post_echo(request_rec *r){
    if (!r->handler || strcmp(r->handler, "post-echo-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int sessions_1(request_rec *r){
    if (!r->handler || strcmp(r->handler, "sessions-1-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}

static int sessions_2(request_rec *r){
    if (!r->handler || strcmp(r->handler, "sessions-2-handler")) return (DECLINED);
    
    ap_set_content_type(r, "text/html");

    return OK;
}