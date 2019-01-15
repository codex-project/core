<?php

namespace Codex\Revisions;

use Codex\Concerns;
use Codex\Contracts\Mergable\ChildInterface;
use Codex\Contracts\Mergable\ParentInterface;
use Codex\Contracts\Revisions\Revision as RevisionContract;
use Codex\Documents\DocumentCollection;
use Codex\Mergable\Concerns\HasChildren;
use Codex\Mergable\Concerns\HasParent;
use Codex\Mergable\Model;

/**
 * This is the class Revision.
 *
 * @package Codex\Revisions
 * @author  Robin Radic
 * @method \Codex\Contracts\Projects\Project getParent()
 * @method \Codex\Documents\DocumentCollection getChildren()
 */
class Revision extends Model implements RevisionContract, ChildInterface, ParentInterface
{
    use Concerns\HasFiles;
    use Concerns\HasCodex;
    use HasParent {
        _setParentAsProperty as setParent;
    }
    use HasChildren {
        _setChildrenProperty as setChildren;
    }
    const DEFAULTS_PATH = 'codex.revisions';

    protected $parent;

    /** @var \Codex\Documents\DocumentCollection */
    protected $children;

    /**
     * Revision constructor.
     *
     * @param array                               $attributes
     * @param \Codex\Documents\DocumentCollection $documents
     */
    public function __construct(array $attributes, DocumentCollection $documents)
    {
        $this->setChildren($documents->setParent($this));
        $registry = $this->getCodex()->getRegistry()->resolveGroup('revisions');
        $this->init($attributes, $registry);
        $this->addGetMutator('inherits', 'getInheritKeys', true, true);
        $this->addGetMutator('changes', 'getChanges', true, true);
    }

    public function url($documentKey = null)
    {
        return $this->getCodex()->url($this->getProject()->getKey(), $this->getKey(), $documentKey);
    }

    public function path(...$parts)
    {
        return path_njoin($this->getKey(), ...$parts);
    }

    public function documents()
    {
        return $this->getDocuments()->toRelationship();
    }

    /**
     * getProject method
     *
     * @return \Codex\Contracts\Projects\Project
     */
    public function getProject()
    {
        return $this->getParent();
    }

    /**
     * @return \Codex\Documents\DocumentCollection
     */
    public function getDocuments()
    {
        return $this->getChildren()->resolve();
    }

    /**
     * getDocument method
     *
     * @param $key
     *
     * @return \Codex\Documents\Document
     */
    public function getDocument($key)
    {
        return $this->getDocuments()->get($key);
    }

    /**
     * hasDocument method
     *
     * @param $key
     *
     * @return bool
     */
    public function hasDocument($key)
    {
        return $this->getDocuments()->has($key);
    }

    public function getDefaultDocumentKey()
    {
        return $this->getDocuments()->getDefaultKey();
    }

}
