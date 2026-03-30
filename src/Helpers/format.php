<?php

declare(strict_types=1);

function e(mixed $value): string
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function excerpt_text(string $text, int $limit = 180): string
{
	$clean = trim(strip_tags($text));
	if ($clean === '') {
		return '';
	}

	if (mb_strlen($clean) <= $limit) {
		return $clean;
	}

	return rtrim(mb_substr($clean, 0, $limit - 1)) . '...';
}

function format_date(?string $date): string
{
	if (!$date) {
		return '';
	}

	try {
		$d = new DateTimeImmutable($date);
		return $d->format('d M Y');
	} catch (Throwable $exception) {
		return $date;
	}
}

function reading_time_minutes(string $content): int
{
	$words = str_word_count(strip_tags($content));
	return max(1, (int) ceil($words / 220));
}
