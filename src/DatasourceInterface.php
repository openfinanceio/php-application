<?php
namespace CFX;

interface DatasourceInterface {
    /**
     * new -- Get a new instance of the Resource class represented by this client
     *
     * @return \CFX\BaseResourceInterface
     */
    public function create();

    /**
     * save -- Send the given resource to the API for saving (either by POST or PATCH)
     *
     * @param \CFX\BaseResourceInterface $r The resource to save
     * @return \CFX\BaseResourceInterface
     */
    public function save(DataObjectInterface $r);

    /**
     * get -- Get resources, optionally filtered by a query
     *
     * @param string $query An optional query with which to filter resources.
     * @return \CFX\BaseResourceInterface|\CFX\ResourceCollectionInterface The resource or resource collection returned
     * by the query. If the query includes an ID, then a single resource is returned (or exception thrown). If it doesn't include an
     * id, then an empty collection may be returned if there are no results.
     *
     * @throws \CFX\ResourceNotFoundException
     */
    public function get($q=null);

    /**
     * delete -- Delete a resource
     *
     * If the resources requested for deletion does not exist, no exception is thrown, since the end goal of the operation is that the
     * resource no longer be in the database.
     *
     * @param \CFX\BaseResourceInterface|id The resource or resource id to delete
     * @return void
     */
    public function delete($r);

    /**
     * getCurrentData -- handshake method between new object and the datasource
     *
     * If this datasource inflates a new object, the new object should use this method in its constructor to get the data retrieved
     * from the datasource. Calling this method should wipe the `currentData` property, such that data is only available to objects
     * directly instantiated by this datasource.
     */
    public function getCurrentData();
}

