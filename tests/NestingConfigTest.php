<?php
namespace Czim\NestedModelUpdater\Test;

use Czim\NestedModelUpdater\Data\RelationInfo;
use Czim\NestedModelUpdater\ModelUpdater;
use Czim\NestedModelUpdater\NestingConfig;
use Czim\NestedModelUpdater\Test\Helpers\AlternativeUpdater;
use Czim\NestedModelUpdater\Test\Helpers\Models\Author;
use Czim\NestedModelUpdater\Test\Helpers\Models\Genre;
use Czim\NestedModelUpdater\Test\Helpers\Models\Post;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NestingConfigTest extends TestCase
{

    /**
     * @test
     */
    function it_returns_whether_an_attribute_key_is_for_a_nested_relation()
    {
        $config = new NestingConfig();

        $this->assertTrue($config->isKeyNestedRelation('genre', Post::class));
        $this->assertFalse($config->isKeyNestedRelation('does_not_exist', Post::class));
    }

    /**
     * @test
     */
    function it_returns_relation_info_object()
    {
        $config = new NestingConfig();

        $info = $config->getRelationInfo('genre', Post::class);

        $this->assertInstanceOf(RelationInfo::class, $info);

        $this->assertTrue($info->isBelongsTo(), "genre should have belongsTo = true");
        $this->assertTrue($info->isSingular(), "genre should have singular = true");
        $this->assertTrue($info->isUpdateAllowed(), "genre should be allowed updates");
        $this->assertEquals(Genre::class, $info->model());
        $this->assertEquals('id', $info->modelPrimaryKey(), "genre primary key should be id");
        $this->assertEquals('genre', $info->relationMethod());
        $this->assertEquals(BelongsTo::class, $info->relationClass());
        $this->assertEquals(ModelUpdater::class, $info->updater());
    }

    /**
     * @test
     */
    function it_returns_relation_info_object_for_exceptions()
    {
        $config = new NestingConfig();

        // check exception for updater
        $info = $config->getRelationInfo('comments', Author::class);
        $this->assertEquals(AlternativeUpdater::class, $info->updater());

        // check exception for relation method
        $info = $config->getRelationInfo('exceptional_attribute_name', Post::class);
        $this->assertEquals('someOtherRelationMethod', $info->relationMethod());

        // only allow links
        $info = $config->getRelationInfo('authors', Post::class);
        $this->assertFalse($info->isUpdateAllowed());
    }
    
}