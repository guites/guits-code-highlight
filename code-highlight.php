<?php
defined('ABSPATH') or die("Acesso direto proibido.");

/**
* Plugin Name: Guits Code Highlight
* Description: Highlight para blocos de código. Controle a linguagem do highlight através das tags!
* Version: 1.0.0
* Author: Guits
* Author URI: http://wordpress.omandriao.com.br/author/umandriao/
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


register_activation_hook( __FILE__, 'code_highlight_activation' );
function code_highlight_activation() {
  # coisas pra fazer ao ativar plugin
  add_option(
    'code_highlight_supported_languages',
    trim("
    markup,css,clike,javascript,abap,abnf,actionscript,ada,agda,al,antlr4,apacheconf,apex,apl,
    applescript,aql,arduino,arff,asciidoc,aspnet,asm6502,autohotkey,autoit,bash,basic,batch,bbcode,
    birb,bison,bnf,brainfuck,brightscript,bro,bsl,c,csharp,cpp,cfscript,chaiscript,cil,clojure,
    cmake,cobol,coffeescript,concurnas,csp,coq,crystal,css-extras,csv,cypher,d,dart,dataweave,dax,
    dhall,diff,django,dns-zone-file,docker,dot,ebnf,editorconfig,eiffel,ejs,elixir,elm,etlua,erb,
    erlang,excel-formula,fsharp,factor,false,firestore-security-rules,flow,fortran,ftl,gml,gcode,gdscript,
    gedcom,gherkin,git,glsl,go,graphql,groovy,haml,handlebars,haskell,haxe,hcl,hlsl,http,hpkp,
    hsts,ichigojam,icon,icu-message-format,idris,ignore,inform7,ini,io,j,java,javadoc,javadoclike,
    javastacktrace,jexl,jolie,jq,jsdoc,js-extras,json,json5,jsonp,jsstacktrace,js-templates,julia,
    keyman,kotlin,kumir,latex,latte,less,lilypond,liquid,lisp,livescript,llvm,log,lolcode,lua,
    makefile,markdown,markup-templating,matlab,mel,mizar,mongodb,monkey,moonscript,n1ql,n4js,
    nand2tetris-hdl,naniscript,nasm,neon,nevod,nginx,nim,nix,nsis,objectivec,ocaml,opencl,openqasm,
    oz,parigp,parser,pascal,pascaligo,psl,pcaxis,peoplecode,perl,php,phpdoc,php-extras,plsql,
    powerquery,powershell,processing,prolog,promql,properties,protobuf,pug,puppet,pure,purebasic,
    purescript,python,qsharp,q,qml,qore,r,racket,jsx,tsx,reason,regex,rego,renpy,rest,
    rip,roboconf,robotframework,ruby,rust,sas,sass,scss,scala,scheme,shell-session,smali,smalltalk,
    smarty,sml,solidity,solution-file,soy,sparql,splunk-spl,sqf,sql,squirrel,stan,iecst,stylus,swift,
    t4-templating,t4-cs,t4-vb,tap,tcl,tt2,textile,toml,turtle,twig,typescript,typoscript,unrealscript,
    uri,v,vala,vbnet,velocity,verilog,vhdl,vim,visual-basic,warpscript,wasm,wiki,xeora,xml-doc,
    xojo,xquery,yaml,yang,zig
    ")
  );
}

register_deactivation_hook( __FILE__, 'code_highlight_deactivation' );
function code_highlight_deactivation() {
  # coisas pra fazer ao desativar plugin
  delete_option('code_highlight_supported_languages');
}

function enqueue_code_plugin_files() {
  if (is_single()) { # se nos encontrarmos em um post do blog

    # acessa a variável global $wp_query, que nos dá, entre outras informações, o ID do post atual

    global $wp_query;
    $postID = $wp_query->post->ID;

    # pega as tags deste post

    $tags = wp_get_post_tags($postID);

    # carrega o prism.css

    wp_enqueue_style(
    'prism_style',
    plugin_dir_url(__FILE__) . 'prism.css',
    array(),
    '1.23.0'
    );

    # pega a listagem das linguas suportadas 

    $highlighted_languages = get_option( 'code_highlight_supported_languages' );

    # caso o hook de ativação não tiver sido carregado corretamente, aborta

    if($highlighted_languages === FALSE){
      return;
    }

    # transforma listagem em um array

    $languages_array = explode(",",$highlighted_languages);

    $highlighted_tags = [];

    foreach($tags as $tag) {
      if (in_array($tag->name,$languages_array)) {
        $highlighted_tags[] = $tag->name;
      }
    }

    # se não encontrarmos nenhuma linguagem nas tags, aborta

    if (count($highlighted_tags) == 0) {
      return;
    }

    # cria um arquivo javascript para ser injetado no post

    wp_register_script( 'code_highlight_adc_class', '', [], '', true );
    wp_enqueue_script( 'code_highlight_adc_class' );

    # adição da lógica para uso do prism.js --- início
    wp_add_inline_script('code_highlight_adc_class',
    "
    var rgx = /em (\w*)$/;
    var code_tags = document.getElementsByTagName('code');
    for (var i = 0; i < code_tags.length; i++) {

      // pega primeira linha dentro da tag code

      var first_line = code_tags[i].innerText.split('\\n')[0];
      var matches = first_line.match(rgx);

      // se a linha estiver no padrão definido, pego o grupo do rgx
      // que equivale a linguagem

      if (matches && matches[1]) {

        var language = matches[1];
        code_tags[i].className = 'language-' + language;

      } else {

        // se o regex não tiver resultado, uso como padrão a linguagem
        // definida pela primeira tag

        code_tags[i].className = 'language-".$highlighted_tags[0]."';
      }
    }
    "
    );
    # adição da lógica para uso do prism.js --- fim

    # adiciona o javacript do prism.js

    wp_register_script('prismjs', plugin_dir_url(__FILE__) . 'prism.js', [], '1.23.0', true);
    wp_enqueue_script('prismjs');
  }
}
add_action( 'wp_enqueue_scripts', 'enqueue_code_plugin_files' );
