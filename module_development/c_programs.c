#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#include "apr_tables.h"
#include "apr_strings.h"
#include "httpd.h" 
#include "http_config.h"
#include "http_protocol.h"
#include "util_cookies.h"
#include "util_script.h"

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

// important reference: https://ci.apache.org/projects/httpd/trunk/doxygen/structrequest__rec.html
static int page_caller(request_rec *r){
    if (!r->handler || strcmp(r->handler, "page-caller-handler")) return (DECLINED);

    char* filename;
    char* filename_prefix;
    int function_index;

    char* directory = "/var/www/darrenwu.xyz/public_html/";

    filename = apr_pstrdup(r->pool, r->filename);
    filename = filename + strlen(directory); // skip over directory part of file name
    filename[strlen(filename)-4] = 0; // Cut off the last 4 characters (.mod)

    apr_table_mergen(r->headers_out, "Cache-Control", "no-cache");

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

static int hello_html(request_rec *r){
    time_t t;
    time(&t);

    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html><head><title>Hello, Apache!</title></head>\
        <body><h1 align=center>Hello, Apache!</h1>\
        <hr/>\n");

    ap_rprintf(r, "Hello, World!<br/>\n");
    ap_rprintf(r, "This program was generated at: %s\n<br/>", ctime(&t));
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

// reference: https://ci.apache.org/projects/httpd/trunk/doxygen/group__apr__tables.html#gabac50c7b2bae5f8cef6245d1959f8b06
// based on: http://dev.ariel-networks.com/apr/apr-tutorial/html/apr-tutorial-19.html#ss19.2
static int print_kv(void *data, const char *key, const char *value){
    request_rec *r = data;
    ap_rprintf(r, "<b>%s</b> : %s<br/>", key, value);
    return TRUE;
}

// source: Nick Kew's Apache Modules Book
static int env(request_rec *r){
    apr_table_t* REQ_HEADERS = r->headers_in;
    apr_table_t* RES_HEADERS = r->headers_out;
    apr_table_t* ENV_HEADERS = r->subprocess_env;

    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html><head><title>Environment Variables</title></head> \
	<body><h1 align=center>Environment Variables</h1> \
  	<hr/>\n");

    // https://ci.apache.org/projects/httpd/trunk/doxygen/group__apr__tables.html#ga5917e542ae910961ee48b0ec2d09a879
    apr_table_do(print_kv, r, REQ_HEADERS, NULL);
    apr_table_do(print_kv, r, RES_HEADERS, NULL);
    apr_table_do(print_kv, r, ENV_HEADERS, NULL);
    
    printf("</body></html>");
    return OK;
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
    apr_table_do(print_kv, r, GET, NULL);

    // Print HTML footer  
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}

typedef struct {
    const char* key;
    const char* value;
} keyValuePair;

// source: https://httpd.apache.org/docs/trunk/developer/modguide.html#get_post
keyValuePair* readPost(request_rec *r) {
    apr_array_header_t* pairs = NULL;
    apr_off_t len;
    apr_size_t size;
    int res;
    int i = 0;
    char* buffer;
    keyValuePair* kvp;

    res = ap_parse_form_data(r, NULL, &pairs, -1, HUGE_STRING_LEN);
    if (res != OK || !pairs) return NULL; /* Return NULL if we failed or if there are is no POST data */
    kvp = apr_pcalloc(r->pool, sizeof(keyValuePair) * (pairs->nelts + 1));
    while (pairs && !apr_is_empty_array(pairs)) {
        ap_form_pair_t *pair = (ap_form_pair_t *) apr_array_pop(pairs);
        apr_brigade_length(pair->value, 1, &len);
        size = (apr_size_t) len;
        buffer = apr_palloc(r->pool, size + 1);
        apr_brigade_flatten(pair->value, buffer, &size);
        buffer[len] = 0;
        kvp[i].key = apr_pstrdup(r->pool, pair->name);
        kvp[i].value = buffer;
        i++;
    }
    return kvp;
}

// source: https://httpd.apache.org/docs/trunk/developer/modguide.html#get_post
static int post_echo(request_rec *r){
    ap_set_content_type(r, "text/html");
    //ap_parse_form_data(r, NULL, &POST, -1, 8192);

    // apr_table_t* POST = r->body_table // something I tried to cheese this function, didn't work in the end

    ap_rprintf(r, "<html><head><title>POST Message Body</title></head>\
        <body><h1 align=center>POST Message Body</h1>\
        <hr/>\n");

    // Get and format query string
    ap_rprintf(r, "Message Body:<br/>");
    keyValuePair* formData = readPost(r);
    if(formData){
        int i;
        for (i = 0; &formData[i]; i++) {
            if (formData[i].key && formData[i].value)
                ap_rprintf(r, "%s = %s<br/>", formData[i].key, formData[i].value);
            else if (formData[i].key)
                ap_rprintf(r, "%s<br/>", formData[i].key);
            else if (formData[i].value)
                ap_rprintf(r, "= %s<br/>", formData[i].value);
            else
                break;
        }
    }

    // Print HTML footer
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}

static int general_request_echo(request_rec *r){
    ap_set_content_type(r, "text/html");
    ap_rprintf(r, "<html><head><title>General Request Echo</title></head> \
	<body><h1 align=center>General Request Echo</h1> \
  	<hr/>\n");

    // Get environment vars
    ap_rprintf(r, "<b>Protocol:</b> %s<br/>\n", r->protocol);
    ap_rprintf(r, "<b>Method:</b> %s<br/>\n", r->method);
    ap_rprintf(r, "<b>Query String and/or Message Body:</b><br/>\n");

    apr_table_t* GET;
    ap_args_to_table(r, &GET);

    keyValuePair* formData = readPost(r);

    apr_table_do(print_kv, r, GET, NULL);
    
    if(formData){
        int i;
        for (i = 0; &formData[i]; i++) {
            if (formData[i].key && formData[i].value)
                ap_rprintf(r, "%s = %s<br/>", formData[i].key, formData[i].value);
            else if (formData[i].key)
                ap_rprintf(r, "%s<br/>", formData[i].key);
            else if (formData[i].value)
                ap_rprintf(r, "= %s<br/>", formData[i].value);
            else
                break;
        }
    }
    
    // Print HTML footer
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");
    return OK;
}

// reference: https://ci.apache.org/projects/httpd/trunk/doxygen/group__APACHE__CORE__COOKIE.html
// cookie logic similar to python cookie logic from before
static int sessions_1(request_rec *r){
    apr_table_t* GET;
    ap_args_to_table(r, &GET);
    const char* username = apr_table_get(GET, "username");

    if(!username || !strcmp(username, "")) // if the value from form is NULL or empty
        ap_cookie_read(r, "username", &username, 0); // get cookie username value from request
    else // if form had something
    // this helped me figure out i needed to add r->headers_out to this line of code: 
    // https://stackoverflow.com/questions/36892699/set-custom-header-to-apache-response-within-a-module
        ap_cookie_write(r, "username", username, NULL, 0, r->headers_out); // write cookie to response
    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html>");
    ap_rprintf(r, "<head>");
    ap_rprintf(r, "<title>Apache Modules Sessions</title>");
    ap_rprintf(r, "</head>");
    ap_rprintf(r, "<body>");
    ap_rprintf(r, "<h1>Apache Modules Page 1</h1>");
    ap_rprintf(r, "<hr/>");
    ap_rprintf(r, "<b>Name:</b> %s<br/>", username);
    ap_rprintf(r, "<a href=\"/sessions-2.mod\">Session Page 2</a><br/>");
    ap_rprintf(r, "<a href=\"/hw2/apache-cgiform.html\">Apache CGI Form</a><br/>");
    ap_rprintf(r, "<form style=\"margin-top:30px\" action=\"/destroy-session.mod\" method=\"get\">");
    ap_rprintf(r, "<button type=\"submit\">Destroy Session</button>");
    ap_rprintf(r, "</form>");

    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}

static int sessions_2(request_rec *r){
    const char* username;
    ap_cookie_read(r, "username", &username, 0);

    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html>");
    ap_rprintf(r, "<head>");
    ap_rprintf(r, "<title>Apache Modules Sessions</title>");
    ap_rprintf(r, "</head>");
    ap_rprintf(r, "<body>");
    ap_rprintf(r, "<h1>Apache Modules Page 2</h1>");
    ap_rprintf(r, "<hr/>");
    ap_rprintf(r, "<b>Name:</b> %s<br/>", username);
    ap_rprintf(r, "<a href=\"/sessions-1.mod\">Session Page 1</a><br/>");
    ap_rprintf(r, "<a href=\"/hw2/apache-cgiform.html\">Apache CGI Form</a><br/>");
    ap_rprintf(r, "<form style=\"margin-top:30px\" action=\"/destroy-session.mod\" method=\"get\">");
    ap_rprintf(r, "<button type=\"submit\">Destroy Session</button>");
    ap_rprintf(r, "</form>");
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}

static int destroy_session(request_rec *r){
    ap_cookie_write(r, "username", "None", NULL, 0, r->headers_out);

    ap_set_content_type(r, "text/html");

    ap_rprintf(r, "<html>");
    ap_rprintf(r, "<head>");
    ap_rprintf(r, "<title>Apache Session Destroyed</title>");
    ap_rprintf(r, "</head>");
    ap_rprintf(r, "<body>");
    ap_rprintf(r, "<h1>Apache Session Destroyed</h1>");
    ap_rprintf(r, "<hr/>");
    ap_rprintf(r, "<a href=\"/hw2/apache-cgiform.html\">Back to the Apache CGI Form</a><br/>");
    ap_rprintf(r, "<a href=\"/sessions-1.mod\">Session Page 1</a><br />");
    ap_rprintf(r, "<a href=\"/sessions-2.mod\">Session Page 2</a><br/>");
    ap_rprintf(r, "</body>");
    ap_rprintf(r, "</html>");

    return OK;
}