<?php
use infrajs\once\Once;

global $infra;
infra_when($infra,'oninitjs', function () {
	global $infra;
	$infra['js'] .= $infra['require']('*once/once.js');
});