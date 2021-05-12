# Plugin para realce de código em wordpress.
Uma explicação detalhada do funcionamento pode ser encontrada em <http://wordpress.omandriao.com.br/criando-um-plugin-para-highlight-de-codigo-com-prism-js/>
## Diretório components
Os arquivos ali podem ser atualizados copiando o conteúdo do [repositório do prism.js](https://github.com/PrismJS/prism)
### Uso
- clone o repositório no dir plugins
- ative no painel
- ao criar um post com código, coloque pelo menos uma tag com um [alias suportado pelo prism.js](https://prismjs.com/#supported-languages)

O código deve ser adicionado no formato
```html
<pre><code><p>Aqui vai ser código.</p>
<div class='body-container'><small>coloque códigos no seus posts.</small></div></code></pre>
```

Para realçar mais de uma linguagem, você precisa escrever
> em _linguagem_
na primeira linha do seu bloco de código.

Exemplo:
```html
<pre><code>em php
<?php echo "I like my php";</pre></code>
```
