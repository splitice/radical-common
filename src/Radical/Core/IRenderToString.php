<?php
namespace Radical\Core;

/**
 * @author SplitIce
 *
 * When it is possible but more costly to produce
 * a string representation of an object, this might be
 * for example the rendered result of a template or 
 * HTTP call
 */
interface IRenderToString{
	function renderString();
}