<?php declare(strict_types=1);

namespace Jawira\Sanitizer\Filters;

use Attribute;

#[Attribute]
class Trim implements FilterInterface
{
  public const BOTH = 'both';
  public const LEFT = 'left';
  public const RIGHT = 'right';

  public function __construct(private string $characters = " \t\n\r\0\x0B",
                              private string $side = self::BOTH)
  {
  }

  /**
   * `trim` function only accepts strings.
   */
  public function check(mixed $value): bool
  {
    return is_string($value);
  }

  /**
   * Apply `trim` function.
   */
  public function filter(mixed $value): string
  {
    assert(is_string($value));

    return match ($this->side) {
      self::LEFT => ltrim($value, $this->characters),
      self::RIGHT => rtrim($value, $this->characters),
      self::BOTH => trim($value, $this->characters),
    };
  }
}
