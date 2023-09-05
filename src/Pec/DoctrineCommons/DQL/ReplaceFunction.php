<?php
declare(strict_types=1);
/*
 * This file is part of the PEC Doctrine Commons package.
 *
 * (c) PEC project engineers & consultants GmbH
 * (c) Oliver Kotte <oliver.kotte@project-engineers.de>
 * (c) Florian Meyer <florian.meyer@project-engineers.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pec\DoctrineCommons\DQL;

use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class ReplaceFunction extends FunctionNode {

	public const IDENTIFIER = 'REPLACE';

	/** @var PathExpression|Node */
	protected Node $stringFirst;
	/** @var PathExpression|Node */
	protected Node $stringSecond;
	/** @var PathExpression|Node */
	protected Node $stringThird;

	/**
	 * {@inheritdoc}
	 * @throws ASTException
	 */
	public function getSql(SqlWalker $sqlWalker): string {
		return self::IDENTIFIER . '(' . $this->stringFirst->dispatch($sqlWalker) . ','
			. $this->stringSecond->dispatch($sqlWalker) . ','
			. $this->stringThird->dispatch($sqlWalker) . ')';
	}

	/**
	 * {@inheritdoc}
	 * @throws QueryException
	 */
	public function parse(Parser $parser): void {
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		$this->stringFirst = $parser->StringPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->stringSecond = $parser->StringPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->stringThird = $parser->StringPrimary();
		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}
}
