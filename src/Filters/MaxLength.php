<?php
/** @noinspection PhpComposerExtensionStubsInspection */
declare(strict_types=1);


namespace Jawira\Sanitizer\Filters;

use Attribute;
use Jawira\Sanitizer\Enums\LengthMode;
use Jawira\Sanitizer\Toolbox\Validate;
use function grapheme_substr;
use function is_string;
use function mb_strcut;
use function mb_substr;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class MaxLength implements FilterInterface
{
  public function __construct(private int        $length,
                              private LengthMode $mode = LengthMode::Characters)
  {
  }

  public function precondition(mixed $value): bool
  {
    return is_string($value);
  }

  public function filter(mixed $value): mixed
  {
    Validate::isString($value, 'Value must be string.');
    assert(is_string($value)); // Tell Psalm $value is string

    $start = 0;
    $length = $this->length;

    // If length is negative cut string from the end.
    if ($this->length < 0) {
      $start = $this->length;
      $length = null;
    }

    return match ($this->mode) {
      LengthMode::Bytes => $this->lengthInBytes($value, $start, $length),
      LengthMode::Characters => $this->lengthInCharacters($value, $start, $length),
      LengthMode::Graphemes => $this->lengthInGraphemes($value, $start, $length),
    };
  }

  private function lengthInBytes(string $value, int $start, ?int $length): string
  {
    Validate::functionExists('mb_strcut', 'mb_strcut function required, install mbstring extension.');

    return mb_strcut($value, $start, $length);
  }

  private function lengthInCharacters(string $value, int $start, ?int $length): string
  {
    Validate::functionExists('mb_substr', 'mb_substr function required, install mbstring extension.');

    return mb_substr($value, $start, $length);
  }

  private function lengthInGraphemes(string $value, int $start, ?int $length): string
  {
    Validate::functionExists('grapheme_substr', 'grapheme_substr function required, install intl extension.');

    return grapheme_substr($value, $start, $length);
  }
}
