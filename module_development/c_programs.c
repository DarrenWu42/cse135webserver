#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#include "apr_tables.h"
#include "apr_strings.h"
#include "httpd.h" 
#include "http_config.h"
#include "http_protocol.h"
#include "util_script.h"

typedef int (*FunctionCallback)(request_rec*);

static void register_hooks(apr_pool_t *pool);
static int page_caller(request_rec *r);

static int print_kv(void *data, const char *key, const char *value);

static int destroy_session(request_rec *r);
static int env(request_rec *r);
static int general_request_echo(request_rec *r);
static int get_echo(request_rec *r);
static int hello_html(request_rec *r);
static int hello_json(request_rec *r);
static int post_echo(request_rec *r);
static int sessions_1(request_rec *r);
static int sessions_2(request_rec *r);

module AP_MODULE_DECLARE_DATA   c_programs_module = { 
    STANDARD20_MODULE_STUFF,
    NULL, /* Per-directory configuration handler */
    NULL,  /* Merge handler for per-directory configurations */
    NULL, /* Per-server configuration handler */
    NULL,  /* Merge handler for per-server configurations */
    NULL,      /* Any directives we may have for httpd */
    register_hooks   /* Our hook registering function */
};

static void register_hooks(apr_pool_t *pool){
    ap_hook_handler(page_caller, NULL, NULL, APR_HOOK_LAST);
}

static int page_caller(request_rec *r){
    if (!r->handler || strcmp(r->handler, "page-caller-handler")) return (DECLINED);

    char* filename;
    char* filename_prefix;
    int function_index;

    char* directory = "/var/www/darrenwu.xyz/public_html/";

    filename = apr_pstrdup(r->pool, r->filename);
    filename = filename + strlen(directory);
    filename[strlen(filename)-4] = 0; // Cut off the last 4 characters (.mod)

    if(strcmp(filename, "destroy-session") == 0)
        return destroy_session(r);
    else if(strcmp(filename, "env") == 0)
        return env(r);
    else if(strcmp(filename, "general-request-echo") == 0)
        return general_request_echo(r);
    else if(strcmp(filename, "get-echo") == 0)
        return get_echo(r);
    else if(strcmp(filename, "hello-html") == 0)
        return hello_html(r);
    else if(strcmp(filename, "hello-json") == 0)
        return hello_json(r);
    else if(strcmp(filename, "post-echo") == 0)
        return post_echo(r);
    else if(strcmp(filename, "sessions-1") == 0)
        return sessions_1(r);
    else if(strcmp(filename, "sessions-2") == 0)
        return sessions_2(r);
    else{
        /*
        ap_set_content_type(r, "text/html");

        ap_rprintf(r, "<html><head><title>Apache Module Error!</title></head>\
            <body><h1 align=center>Apache Module Error!</h1>\
            <hr/>\n");

        ap_rprintf(r, "<b>Filename: </b>%s", filename);
        ap_rprintf(r, "<br/>\n");
        ap_rprintf(r, "</body></html>");
        //*/
        return HTTP_NOT_FOUND;
    }
}

static int destroy_session(request_rec *r){
    ap_set_content_type(r, "text/html");

    return OK;
}

static int env(request_rec *r){
    ap_set_content_type(r, "text/html");

    return OK;
}

static int general_request_echo(request_rec *r){
    apr_table_t* GET; 
    apr_array_header_t* POST;

    ap_args_to_table(r, &GET); 
    ap_parse_form_data(r, NULL, &POST, -1, 8192);

    ap_set_content_type(r, "text/html");

    return OK;
}

static int print_kv(void *data, const char *key, const char *value){
    printf("<b>%s</b> = %s\n", key, value);
    return TRUE;
}

static int get_echo(request_rec *r){
    apr_table_t* GET;
    ap_args_to_table(r, &GET);
    
    ap_set_content_type(r, "text/html");
    ap_rprintf(r, "<html><head><title>GET Request Echo</title></head>\
        <body><h1 align=center>GET Request Echo</h1>\
        <hr/>\n");

    // Get and format query string
    ap_rprintf(r, "Raw query string: %s<br/><br/>", r->args);
    ap_rprintf(r, "Formatted Query String:<br/>");
    apr_table_do(print_kv, NULL, GET, NULL);

    // Print HTML footer  
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}

static int hello_html(request_rec *r){
    time_t t;
    time(&t);

    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html><head><title>Hello, Apache!</title></head>\
        <body><h1 align=center>Hello, Apache!</h1>\
        <hr/>\n");

    ap_rprintf(r, "Hello, World!<br/>\n");
    ap_rprintf(r, "This program was generated at: %s\n<br/>", ctime(&t));
    ap_rprintf(r, "This program filename is: %s\n<br/>", r->filename);
    ap_rprintf(r, "Your current IP address is: %s<br/>", r->useragent_ip);
    
    // Print HTML footer
    ap_rprintf(r, "</body></html>");

    return OK;
}

static int hello_json(request_rec *r){
    time_t t;
	time(&t);

    char *buffer = ctime(&t);
	buffer[strlen(buffer) - 1] = '\0';

    ap_set_content_type(r, "application/json");
	ap_rprintf(r, "{\n\t\"message\": \"Hello, C!\",\n");
	ap_rprintf(r, "\t\"date\": \"%s\",\n", buffer);
	ap_rprintf(r, "\t\"currentIP\": \"%s\"\n}\n", r->useragent_ip);

    return OK;
}

// source: https://httpd.apache.org/docs/trunk/developer/modguide.html#get_post
static int post_echo(request_rec *r){
    ap_set_content_type(r, "text/html");
    
    apr_array_header_t* POST;
    ap_parse_form_data(r, NULL, &POST, -1, 8192);

    printf("<html><head><title>POST Message Body</title></head>\
        <body><h1 align=center>POST Message Body</h1>\
        <hr/>\n");

    // Print HTML footer
    printf("</body>");
    printf("</html>");

    return OK;
}

static int sessions_1(request_rec *r){
    ap_set_content_type(r, "text/html");

    return OK;
}

static int sessions_2(request_rec *r){
    ap_set_content_type(r, "text/html");

    return OK;
}