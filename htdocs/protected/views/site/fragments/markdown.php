<div id="fragment-markdown-help-bg" style="position: fixed; z-index: 999; left: 0; right: 0; top: 0; bottom: 0; opacity: .6; filter: alpha(opacity=60); background: black;" ></div><div id="fragment-markdown-help" style="position: absolute; z-index: 1000; left: 10%; right: 10%; top: 10%; background: #E2FFD2; border: 1px black solid; border-radius: 5px; padding: 8px;">
<h2>Formatting</h2>
Task descriptions and comments are formatted using Markdown.  Some simple HTML tags are supported, and there is also support for code syntax highlighting.

<h3>Basic formatting</h3>
<p>Paragraphs are seperated by a blank line.</p>
<p>*word* or _word_ becomes <em>word</em></p>
<p>**word** or __word__ becomes <strong>word</strong></p>
<p>You can create a "blockquote" by starting a paragraph with &gt;</p>

<h3>Lists</h3>
<p>To create a list of unordered items simply create a new paragraph and start each item with a *, +, or -</p>
<pre>
 * C++
 * Java
 * PHP
</pre>
<p>If you want an ordered (numbered) list use numbers followed by periods instead of *, +, or -.</p>
<pre>
 1. C++
 2. Java
 3. PHP
</pre>

<h3>Source Code</h3>
<p>To put in source code start each line with at least 4 spaces.  Alternatively you may create blocks of source code "fenced" by three or more tilde characters.  The number of tilde characters doesn't matter as long as the start and end match.</p>
<p>When creating a fenced code block if the first inside the block contains square brackets, then it is used to determine the language and options.  You may specify any of:  ABAP, CPP, CSS, DIFF, DTD, HTML, JAVA, JAVASCRIPT, MYSQL, PERL, PHP, PYTHON, RUBY, SQL, XML</p>
<p>You may also, optionally, add showLineNumbers=1 to the square brackets and the code will show line numbers.</p>
<p>I.e. you may do:</p>
<pre>
~~~~~~~
[Java showLineNumbers=1]
class TestClass
{
}
~~~~~~~
</pre>
<h3>Further Reading</h3>
You can find more information on both the <a href="http://daringfireball.net/projects/markdown/syntax">official markdown site</a> and the <a href="http://michelf.com/projects/php-markdown/extra/">extended markdown site</a>.
<script type="text/javascript">
var htRoot = $('html');
htRoot.click(function(e){
	$('#fragment-markdown-help').remove();
	$('#fragment-markdown-help-bg').remove();
	htRoot.unbind('click');
});
var fh = $('#fragment-markdown-help');
var of = fh.offset();
of.top += $('html').scrollTop();
fh.offset( of );
</script>
</div>
