<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace Facebook\GFM;

use namespace HH\Lib\{C, Str, Vec};
use function Facebook\GFM\_Private\plain_text_to_html;
use function Facebook\GFM\_Private\plain_text_to_html_attribute;

// TODO: fix namespace support in XHP, use that :'(
function render_html(ASTNode $node): string {
  if ($node instanceof Blocks\BlankLine) {
    return "\n";
  }

  if ($node instanceof Blocks\BlockQuote) {
    return $node->getChildren()
      |> Vec\map($$, $child ==> render_html($child))
      |> Str\join($$, '')
      |> '<blockquote>'.$$.'</blockquote>';
  }

  if ($node instanceof Blocks\Document) {
    return $node->getChildren()
      |> Vec\map($$, $child ==> render_html($child))
      |> Str\join($$, '');
  }

  // Blocks\FencedCodeBlock
  // Blocks\IndentedCodeBlock
  if ($node instanceof Blocks\CodeBlock) {
    $extra = '';
    $info = $node->getInfoString();
    if ($info !== null) {
      $first = C\firstx(Str\split($info, ' '));
      $extra = ' class="language-'.plain_text_to_html_attribute($first).'"';
    }
    return plain_text_to_html($node->getCode())
      |> '<pre><code'.$extra.'>'.$$."\n</code></pre>";
  }

  if ($node instanceof Blocks\Heading) {
    $level = $node->getLevel();
    return $node->getHeading()
      |> Vec\map($$, $child ==> render_html($child))
      |> Str\join($$, '')
      |> sprintf("<h%d>%s</h%d>", $level, $$, $level);
  }

  if ($node instanceof Blocks\HTMLBlock) {
    return $node->getCode();
  }

  if ($node instanceof Blocks\LinkReferenceDefinition) {
    return '';
  }

  if ($node instanceof Blocks\ListItem) {
    $children = $node->getChildren();
    $child = C\first($children);
    if (C\count($children) === 1 && $child instanceof Blocks\Paragraph) {
      $children = $child->getContents();
    }
    return $children
      |> Vec\map($$, $child ==> render_html($child))
      |> Str\join($$, "\n")
      |> '<li>'.$$.'</li>';
  }

  if ($node instanceof Blocks\ListOfItems) {
    $start = $node->getFirstNumber();
    if ($start === null) {
      $start = '<ul>';
      $end = '</ul>';
    } else {
      $start = sprintf('<ol start="%d">', $start);
      $end = '</ol>';
    }
    return $node->getItems()
      |> Vec\map($$, $item ==> render_html($item))
      |> Str\join($$, "\n")
      |> $start."\n".$$."\n".$end."\n";
  }

  if ($node instanceof Blocks\Paragraph) {
    return $node->getContents()
      |> Vec\map($$, $item ==> render_html($item))
      |> Str\join($$, '')
      |> '<p>'.$$."</p>\n";
  }

  if ($node instanceof Blocks\ThematicBreak) {
    return "<hr />\n";
  }

  if ($node instanceof Inlines\AutoLink) {
    // TODO
  }

  // Inlines\BackslashEscape
  // Inlines\DisallowedRawHTML
  // Inlines\TextualContent
  if ($node instanceof Inlines\InlineWithPlainTextContent) {
    return plain_text_to_html($node->getContent());
  }

  if ($node instanceof Inlines\CodeSpan) {
    return '<code>'.plain_text_to_html($node->getCode()).'</code>';
  }

  if ($node instanceof Inlines\Emphasis) {
    // TODO
  }

  if ($node instanceof Inlines\EntityReference) {
    // TODO
  }

  if ($node instanceof Inlines\HardLineBreak) {
    return "<br />\n";
  }

  if ($node instanceof Inlines\Image) {
    // TODO
  }

  if ($node instanceof Inlines\Link) {
    // TODO
  }

  if ($node instanceof Inlines\RawHTML) {
    // TODO
  }

  if ($node instanceof Inlines\SoftLineBreak) {
    return "\n";
  }

  if ($node instanceof Inlines\Strikethrough) {
    // TODO
  }

  invariant_violation(
    "Unhandled node type: %s",
    get_class($node),
  );
}